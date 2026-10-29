<?php

namespace App\Tracking\Http\Controllers\Admin;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams report rows as a CSV download. Cells that a spreadsheet would run
 * as a formula (=, +, -, @) are prefixed with a quote, since page titles and
 * link labels come from the public.
 */
final class CsvExport
{
    /**
     * @param array<string, string> $columns row key => column heading
     * @param iterable<array<string, mixed>> $rows
     */
    public static function download(string $name, array $columns, iterable $rows): StreamedResponse
    {
        $filename = preg_replace('/[^a-z0-9-]+/i', '-', $name) . '-' . date('Y-m-d') . '.csv';

        return new StreamedResponse(function () use ($columns, $rows) {
            $out = fopen('php://output', 'wb');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 marker, so Excel shows accents correctly
            fputcsv($out, array_values($columns));
            foreach ($rows as $row) {
                fputcsv($out, array_map(fn ($key) => self::cell($row[$key] ?? ''), array_keys($columns)));
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-store',
        ]);
    }

    private static function cell(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }
}
