<?php

namespace App\Helpers;

class EmailVerifierHelper
{
    protected static array $disposableDomains = [
        'mailinator.com','yopmail.com','guerrillamail.com','guerrillamail.info','sharklasers.com',
        'temp-mail.org','tempmail.com','tempmail.net','10minutemail.com','10minutemail.net',
        'trashmail.com','trashmail.net','throwawaymail.com','getnada.com','mailnesia.com',
        'dispostable.com','fakeinbox.com','mintemail.com','mohmal.com','moakt.com',
        'emailondeck.com','maildrop.cc','mailcatch.com','spamgourmet.com','anonbox.net',
        'discard.email','tempinbox.com','tempr.email','burnermail.io','inboxbear.com',
        'mailsac.com','tempmailo.com','mailtemp.net','fakemail.net','byom.de',
    ];

    protected static array $freeProviders = [
        'gmail.com','yahoo.com','outlook.com','hotmail.com','icloud.com','aol.com',
        'protonmail.com','proton.me','live.com','msn.com','yandex.com','zoho.com',
        'gmx.com','mail.com','rediffmail.com',
    ];

    protected static array $roleLocalParts = [
        'admin','administrator','info','support','sales','contact','hello','help',
        'office','billing','marketing','noreply','no-reply','postmaster','webmaster',
        'abuse','careers','jobs','press','enquiries','feedback','service','team',
    ];

    public static function verify(string $rawEmail): array
    {
        $email = strtolower(trim($rawEmail));

        $result = [
            'email' => $rawEmail,
            'status' => 'invalid',
            'reason' => '',
            'checks' => [
                'syntax' => false,
                'mx_record' => false,
                'disposable' => false,
                'free_provider' => false,
                'role_based' => false,
                'smtp' => 'skipped',
                'catch_all' => 'unknown',
            ],
        ];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || substr_count($email, '@') !== 1) {
            $result['reason'] = 'This is not a valid email address format.';
            return $result;
        }
        $result['checks']['syntax'] = true;

        [$local, $domain] = explode('@', $email, 2);

        if (in_array($domain, self::$disposableDomains, true)) {
            $result['checks']['disposable'] = true;
            $result['status'] = 'disposable';
            $result['reason'] = 'This domain is a known disposable/temporary email provider.';
            return $result;
        }

        if (in_array($local, self::$roleLocalParts, true)) {
            $result['checks']['role_based'] = true;
        }

        if (in_array($domain, self::$freeProviders, true)) {
            $result['checks']['free_provider'] = true;
        }

        $mxHosts = [];
        $mxWeights = [];
        $hasMx = @getmxrr($domain, $mxHosts, $mxWeights);
        if (!$hasMx || empty($mxHosts)) {
            if (!checkdnsrr($domain, 'A')) {
                $result['reason'] = 'This domain has no mail server (MX/A record) and cannot receive email.';
                return $result;
            }
            $mxHosts = [$domain];
            $mxWeights = [0];
        }
        $result['checks']['mx_record'] = true;

        array_multisort($mxWeights, $mxHosts);
        $mxHost = rtrim($mxHosts[0], '.');

        $ip = @gethostbyname($mxHost);
        $isSafeIp = $ip !== $mxHost && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

        if ($isSafeIp && get_static_option('email_verifier_enable_smtp_check', '1') == '1') {
            $probe = self::smtpProbe($ip, $email, $domain);
            $result['checks']['smtp'] = $probe['status'];

            if ($probe['status'] === 'rejected') {
                $result['status'] = 'invalid';
                $result['reason'] = 'The mail server rejected this address, it does not appear to exist.';
                return $result;
            }

            if ($probe['status'] === 'accepted' && get_static_option('email_verifier_enable_catchall_check', '1') == '1') {
                $randomLocal = 'not-a-real-user-' . bin2hex(random_bytes(5));
                $catchAllProbe = self::smtpProbe($ip, $randomLocal . '@' . $domain, $domain);
                if ($catchAllProbe['status'] === 'accepted') {
                    $result['checks']['catch_all'] = 'yes';
                    $result['status'] = 'catch_all';
                    $result['reason'] = "This domain accepts mail for any address (catch-all), so we can't fully confirm this specific mailbox exists.";
                    return $result;
                }
                $result['checks']['catch_all'] = 'no';
            }
        } else {
            $result['checks']['smtp'] = 'unknown';
        }

        if ($result['checks']['role_based']) {
            $result['status'] = 'role_based';
            $result['reason'] = 'This looks like a shared/role inbox (e.g. info@, support@) rather than a personal mailbox.';
            return $result;
        }

        if ($result['checks']['smtp'] === 'unknown') {
            $result['status'] = 'unknown';
            $result['reason'] = 'Syntax and domain checks passed, but the mailbox itself could not be confirmed live right now.';
            return $result;
        }

        $result['status'] = 'valid';
        $result['reason'] = 'This address passed every check and appears safe to send to.';
        return $result;
    }

    protected static function smtpProbe(string $ip, string $emailToCheck, string $domain): array
    {
        $timeout = (int) (get_static_option('email_verifier_smtp_timeout') ?: 8);
        $heloDomain = get_static_option('email_verifier_sender_domain') ?: parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $mailFrom = get_static_option('email_verifier_sender_email') ?: ('verify@' . $heloDomain);

        $errno = 0;
        $errstr = '';
        $conn = @fsockopen($ip, 25, $errno, $errstr, $timeout);
        if (!$conn) {
            return ['status' => 'unknown'];
        }
        stream_set_timeout($conn, $timeout);

        $read = function () use ($conn) {
            $data = '';
            while (($line = fgets($conn, 515)) !== false) {
                $data .= $line;
                if (strlen($line) < 4 || $line[3] === ' ') {
                    break;
                }
            }
            $meta = stream_get_meta_data($conn);
            if (!empty($meta['timed_out'])) {
                return '';
            }
            return $data;
        };

        $write = function ($cmd) use ($conn) {
            @fwrite($conn, $cmd . "\r\n");
        };

        $banner = $read();
        if (substr($banner, 0, 3) !== '220') {
            fclose($conn);
            return ['status' => 'unknown'];
        }

        $write('EHLO ' . $heloDomain);
        $read();

        $write('MAIL FROM: <' . $mailFrom . '>');
        $mailFromResp = $read();
        if (substr($mailFromResp, 0, 1) !== '2') {
            @fwrite($conn, "QUIT\r\n");
            fclose($conn);
            return ['status' => 'unknown'];
        }

        $write('RCPT TO: <' . $emailToCheck . '>');
        $rcptResp = $read();
        $write('QUIT');
        fclose($conn);

        $code = (int) substr($rcptResp, 0, 3);
        if ($code >= 200 && $code < 300) {
            return ['status' => 'accepted'];
        }
        if (in_array($code, [550, 551, 553, 554], true)) {
            // A 550-series code can mean "mailbox doesn't exist" OR "we don't trust this
            // sending IP" (dynamic/residential IP policy blocks, rate limiting, reputation
            // blocks). The latter is common from Zoho/Google/Microsoft when the probing
            // server has no sending reputation, it is not evidence the address is invalid.
            if (preg_match('/polic|dynamic|residential|reputation|spamhaus|rate.?limit|greylist|throttl|not authorized|denied due to/i', $rcptResp)) {
                return ['status' => 'unknown'];
            }
            return ['status' => 'rejected'];
        }
        return ['status' => 'unknown'];
    }
}
