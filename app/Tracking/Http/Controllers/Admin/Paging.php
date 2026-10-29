<?php

namespace App\Tracking\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/** Page number, offset and the paginator links for a table whose rows a report fetched. */
final class Paging
{
    public const EXPORT_LIMIT = 5000;

    public int $page;

    public function __construct(private Request $request, public int $perPage = 50)
    {
        $this->page = max(1, (int) $request->query('page', 1));
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    /** @param list<mixed> $rows */
    public function paginator(int $total, array $rows): LengthAwarePaginator
    {
        return (new LengthAwarePaginator($rows, $total, $this->perPage, $this->page, [
            'path' => $this->request->url(),
        ]))->withQueryString();
    }
}
