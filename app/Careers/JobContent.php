<?php

namespace App\Careers;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * A job description section, cleaned for the role page. The sections are
 * pasted into the admin editor from Google Docs, which brings 14pt Arial,
 * grey colours, spans on every phrase and empty paragraphs with it. At
 * render (the database is left as written) this keeps the structure (headings,
 * paragraphs, lists, bold, links, tables) and drops the styling.
 */
final class JobContent
{
    /** Tags kept as they are; anything else is unwrapped to its contents. */
    private const KEEP = ['p', 'br', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'u', 'a', 'h2', 'h3', 'h4',
        'h5', 'h6', 'blockquote', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'hr', 'img'];

    /** Dropped with their contents. */
    private const DROP = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'meta', 'link'];

    private const ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan'],
        'ol' => ['start'],
    ];

    /** True when the section has no words or images: a heading over it would stand alone. */
    public static function isBlank(?string $html): bool
    {
        if ($html === null) {
            return true;
        }
        $text = html_entity_decode(strip_tags($html, '<img>'), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(str_replace("\u{00A0}", ' ', $text)) === '';
    }

    public static function clean(?string $html): string
    {
        if (self::isBlank($html)) {
            return '';
        }

        $doc = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"?><div id="g4-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = (new DOMXPath($doc))->query('//*[@id="g4-root"]')->item(0);
        if (!$root instanceof DOMElement) {
            return '';
        }
        self::tidy($root);
        self::dropEmpty($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }

    private static function tidy(DOMElement $element): void
    {
        foreach (iterator_to_array($element->childNodes) as $child) {
            if (!$child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            if (in_array($tag, self::DROP, true)) {
                $element->removeChild($child);
                continue;
            }
            self::tidy($child);
            // The page title is the only H1: a section's headings start at H2.
            if ($tag === 'h1') {
                $child = self::rename($child, 'h2');
                $tag = 'h2';
            }
            // Google Docs writes bold as a styled span, and wraps the whole
            // paste in a <b style="font-weight:normal">; read the style
            // before it goes.
            $style = strtolower(str_replace(' ', '', (string) $child->getAttribute('style')));
            if (in_array($tag, ['b', 'strong'], true) && preg_match('/font-weight:(normal|[1-4]00)/', $style)) {
                self::unwrap($child);
                continue;
            }
            if ($tag === 'span' && preg_match('/font-weight:(bold|[6-9]00)/', $style)) {
                $child = self::rename($child, 'strong');
                $tag = 'strong';
            } elseif ($tag === 'span' && str_contains($style, 'font-style:italic')) {
                $child = self::rename($child, 'em');
                $tag = 'em';
            }
            if (!in_array($tag, self::KEEP, true)) {
                self::unwrap($child);
                continue;
            }
            self::keepAttributes($child, self::ATTRIBUTES[$tag] ?? []);
            if ($tag === 'a') {
                if (preg_match('#^\s*javascript:#i', (string) $child->getAttribute('href'))) {
                    $child->removeAttribute('href');
                }
                if ($child->getAttribute('target') === '_blank') {
                    $child->setAttribute('rel', 'noopener');
                }
            }
        }
    }

    /** Empty paragraphs, list items and headings, and line breaks at the start or end of a block. */
    private static function dropEmpty(DOMElement $root): void
    {
        $xpath = new DOMXPath($root->ownerDocument);
        foreach (iterator_to_array($xpath->query('.//p|.//li|.//h2|.//h3|.//h4|.//h5|.//h6|.//ul|.//ol', $root)) as $node) {
            if ($node->parentNode !== null && self::isBlank($root->ownerDocument->saveHTML($node))) {
                $node->parentNode->removeChild($node);
            }
        }
        foreach (iterator_to_array($xpath->query('.//p|.//li|.//h2|.//h3|.//h4|.//td', $root)) as $block) {
            self::trimBreaks($block);
        }
    }

    private static function trimBreaks(DOMElement $block): void
    {
        foreach (['firstChild', 'lastChild'] as $end) {
            while (($node = $block->{$end}) !== null) {
                $blankText = $node->nodeType === XML_TEXT_NODE && trim(str_replace("\u{00A0}", ' ', $node->textContent)) === '';
                $break = $node instanceof DOMElement && strtolower($node->tagName) === 'br';
                if (!$blankText && !$break) {
                    break;
                }
                $block->removeChild($node);
            }
        }
    }

    private static function keepAttributes(DOMElement $element, array $allowed): void
    {
        foreach (iterator_to_array($element->attributes) as $attribute) {
            if (!in_array(strtolower($attribute->name), $allowed, true)) {
                $element->removeAttribute($attribute->name);
            }
        }
    }

    private static function unwrap(DOMNode $node): void
    {
        $parent = $node->parentNode;
        while ($node->firstChild !== null) {
            $parent->insertBefore($node->firstChild, $node);
        }
        $parent->removeChild($node);
    }

    private static function rename(DOMElement $element, string $tag): DOMElement
    {
        $renamed = $element->ownerDocument->createElement($tag);
        while ($element->firstChild !== null) {
            $renamed->appendChild($element->firstChild);
        }
        $element->parentNode->replaceChild($renamed, $element);

        return $renamed;
    }
}
