<?php

namespace App\Helpers;

/**
 * Works out the most likely work email for a person at a company.
 *
 * Extends EmailVerifierHelper on purpose: the finder needs the exact same
 * SMTP probing the verifier already does (including its handling of policy
 * blocks vs. genuine "no such mailbox" rejections), so it reuses that rather
 * than keeping a second copy of the same protocol logic in the codebase.
 *
 * Order of work matters for speed. Each SMTP probe can take up to the
 * configured timeout (8s by default), so probing ten patterns one by one
 * could take over a minute. Instead:
 *   1. one DNS lookup to find the mail server,
 *   2. one probe for a deliberately fake address to see if the domain is
 *      catch-all (accepts everything) -- if it is, probing real candidates
 *      proves nothing, so we stop and say so honestly,
 *   3. otherwise probe candidates in likelihood order and stop at the first
 *      one the server accepts.
 * The most common pattern is also the first one tried, so the usual case
 * resolves in about two probes.
 */
class EmailFinderHelper extends EmailVerifierHelper
{
    /**
     * Defaults for the two latency dials. Both are admin-editable under
     * General Settings -> Email Finder Settings; these are what they fall
     * back to when nothing is saved.
     */
    protected const MAX_PROBES = 6;
    protected const UNKNOWN_BAIL = 2;

    /** How many candidates we are willing to probe before giving up, for latency. */
    protected static function maxProbes(): int
    {
        $saved = (int) get_static_option('email_finder_max_probes');

        return ($saved >= 1 && $saved <= 10) ? $saved : self::MAX_PROBES;
    }

    /** How many no-answer probes before we stop and label the result a guess. */
    protected static function unknownBail(): int
    {
        $saved = (int) get_static_option('email_finder_unknown_bail');

        return ($saved >= 1 && $saved <= 10) ? $saved : self::UNKNOWN_BAIL;
    }

    /**
     * Patterns in descending real-world frequency. first.last is roughly 60%
     * of corporate addresses, first@ another 15-20%, flast@ 10-15%; the rest
     * share what's left.
     */
    protected static function patterns(string $first, string $last): array
    {
        $f = substr($first, 0, 1);
        $l = substr($last, 0, 1);

        $candidates = [
            ['local' => "{$first}.{$last}", 'label' => 'first.last'],
            ['local' => $first,             'label' => 'first'],
            ['local' => "{$f}{$last}",      'label' => 'flast'],
            ['local' => "{$first}{$last}",  'label' => 'firstlast'],
            ['local' => "{$f}.{$last}",     'label' => 'f.last'],
            ['local' => "{$first}_{$last}", 'label' => 'first_last'],
            ['local' => "{$first}{$l}",     'label' => 'firstl'],
            ['local' => "{$last}.{$first}", 'label' => 'last.first'],
            ['local' => "{$last}{$f}",      'label' => 'lastf'],
            ['local' => $last,              'label' => 'last'],
        ];

        // A single-name input (no surname) collapses several of these to the
        // same string; keep the first occurrence of each.
        $seen = [];
        $unique = [];
        foreach ($candidates as $candidate) {
            if ($candidate['local'] === '' || isset($seen[$candidate['local']])) {
                continue;
            }
            $seen[$candidate['local']] = true;
            $unique[] = $candidate;
        }

        return $unique;
    }

    /**
     * Strips everything that cannot appear in the local part of an address:
     * accents are folded to ASCII, then anything left that isn't a letter,
     * digit, dot, underscore or hyphen is dropped.
     */
    protected static function normaliseName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '';
        }

        $folded = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        if ($folded !== false) {
            $name = $folded;
        }

        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9._-]/', '', $name);

        return trim($name, '._-');
    }

    /** Accepts a bare domain or a full URL and returns just the hostname. */
    protected static function normaliseDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = explode('/', $domain)[0];

        return trim($domain);
    }

    public static function find(string $rawFirst, string $rawLast, string $rawDomain): array
    {
        $first = self::normaliseName($rawFirst);
        $last = self::normaliseName($rawLast);
        $domain = self::normaliseDomain($rawDomain);

        $result = [
            'status' => 'not_found',
            'email' => null,
            'pattern' => null,
            'confidence' => 'low',
            'reason' => '',
            'domain' => $domain,
            'candidates' => [],
            'checked' => 0,
        ];

        if ($first === '') {
            $result['reason'] = 'Enter at least a first name to search with.';
            return $result;
        }

        if ($domain === '' || !preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/', $domain)) {
            $result['reason'] = 'That company domain does not look valid. Try something like acme.com.';
            return $result;
        }

        if (in_array($domain, self::$freeProviders, true)) {
            $result['reason'] = 'That is a personal email provider, not a company domain. Work emails follow a company pattern; free inboxes do not.';
            return $result;
        }

        if (in_array($domain, self::$disposableDomains, true)) {
            $result['reason'] = 'That domain is a disposable/temporary mail provider, so there is no real work address to find.';
            return $result;
        }

        $candidates = self::patterns($first, $last);
        $result['candidates'] = array_map(function ($candidate) use ($domain) {
            return [
                'email' => $candidate['local'] . '@' . $domain,
                'pattern' => $candidate['label'],
            ];
        }, $candidates);

        // Does the domain receive mail at all?
        $mxHosts = [];
        $mxWeights = [];
        if (!@getmxrr($domain, $mxHosts, $mxWeights) || empty($mxHosts)) {
            if (!checkdnsrr($domain, 'A')) {
                $result['status'] = 'no_mail_server';
                $result['reason'] = 'This domain has no mail server, so it cannot receive email at any address.';
                $result['candidates'] = [];
                return $result;
            }
            $mxHosts = [$domain];
            $mxWeights = [0];
        }

        array_multisort($mxWeights, $mxHosts);
        $mxHost = rtrim($mxHosts[0], '.');
        $ip = @gethostbyname($mxHost);
        $isSafeIp = $ip !== $mxHost && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

        if (!$isSafeIp || get_static_option('email_verifier_enable_smtp_check', '1') != '1') {
            $result['status'] = 'best_guess';
            $result['email'] = $result['candidates'][0]['email'];
            $result['pattern'] = $result['candidates'][0]['pattern'];
            $result['confidence'] = 'low';
            $result['reason'] = 'We could not reach this domain\'s mail server to confirm, so this is the statistically most common pattern rather than a confirmed address.';
            return $result;
        }

        // Catch-all domains accept mail for any address, real or not, so
        // probing individual candidates there tells us nothing. Turning this
        // off saves one probe per lookup, at the cost of reporting catch-all
        // domains as ordinary confirmed hits, which they are not.
        $catchAllProbe = get_static_option('email_finder_enable_catchall_check', '1') == '1'
            ? self::smtpProbe($ip, 'not-a-real-user-' . bin2hex(random_bytes(5)) . '@' . $domain, $domain)
            : ['status' => 'skipped'];
        if ($catchAllProbe['status'] === 'accepted') {
            $result['status'] = 'catch_all';
            $result['email'] = $result['candidates'][0]['email'];
            $result['pattern'] = $result['candidates'][0]['pattern'];
            $result['confidence'] = 'low';
            $result['reason'] = 'This domain accepts mail at any address (catch-all), so no single address can be confirmed. Shown below is the most common pattern for a company like this.';
            return $result;
        }

        // "unknown" means the server gave us no usable answer (policy block,
        // greylisting, no sending reputation on our IP). Large providers do
        // this to every probe, so once a couple come back that way, the rest
        // will too -- keep probing and the visitor waits ~10s to learn nothing.
        $inconclusive = 0;
        $maxProbes = self::maxProbes();
        $unknownBail = self::unknownBail();

        foreach ($candidates as $index => $candidate) {
            if ($index >= $maxProbes) {
                break;
            }

            $email = $candidate['local'] . '@' . $domain;
            $probe = self::smtpProbe($ip, $email, $domain);
            $result['checked']++;

            if ($probe['status'] === 'accepted') {
                $result['status'] = 'found';
                $result['email'] = $email;
                $result['pattern'] = $candidate['label'];
                // The first pattern is also the most common one, so a hit
                // there is the strongest signal available.
                $result['confidence'] = $index === 0 ? 'high' : 'medium';
                $result['reason'] = 'The mail server accepted this address, so this mailbox exists.';
                return $result;
            }

            if ($probe['status'] === 'unknown') {
                $inconclusive++;
                if ($inconclusive >= $unknownBail) {
                    $result['status'] = 'best_guess';
                    $result['email'] = $result['candidates'][0]['email'];
                    $result['pattern'] = $result['candidates'][0]['pattern'];
                    $result['confidence'] = 'low';
                    $result['reason'] = 'This mail server does not answer address checks from outside senders, so no address here can be confirmed either way. The one below is the most common pattern for this kind of company.';
                    return $result;
                }
            }
        }

        $result['status'] = 'best_guess';
        $result['email'] = $result['candidates'][0]['email'];
        $result['pattern'] = $result['candidates'][0]['pattern'];
        $result['confidence'] = 'low';
        $result['reason'] = 'None of the usual patterns were accepted by this mail server. The address below is the most common format, but it is a guess rather than a confirmed mailbox.';

        return $result;
    }
}
