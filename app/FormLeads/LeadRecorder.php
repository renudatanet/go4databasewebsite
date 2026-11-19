<?php

namespace App\FormLeads;

use App\B2BList;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Saves a form submission as a form lead, with where it came from: the list
 * page (its id and title as they were then), the page address, and the
 * visitor's IP and browser.
 */
final class LeadRecorder
{
    /**
     * @param array{name?: ?string, email?: ?string, phone?: ?string, company?: ?string, message?: ?string} $details
     */
    public function record(FormKind $kind, array $details, Request $request): FormLead
    {
        $list = $this->listFrom($request);

        return FormLead::create([
            'form' => $kind->value,
            'name' => $this->clip($details['name'] ?? null, 191),
            'email' => $this->clip(isset($details['email']) ? Str::lower(trim($details['email'])) : null, 191),
            'phone' => $this->clip($details['phone'] ?? null, 60),
            'company' => $this->clip($details['company'] ?? null, 500),
            'message' => $this->clip($details['message'] ?? null, 5000),
            'list_id' => $list?->id,
            'list_title' => $this->clip($list?->title, 191),
            'page_url' => $this->clip($this->pageUrl($request), 500),
            'ip' => $request->ip(),
            'user_agent' => $this->clip($request->userAgent(), 500),
            'status' => 'new',
        ]);
    }

    /** The list page the form was on, from its hidden list_id. */
    private function listFrom(Request $request): ?B2BList
    {
        $id = filter_var($request->input('list_id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        return $id === false ? null : B2BList::query()->select(['id', 'title'])->find($id);
    }

    /** The page the visitor sent the form from, when it is a page of this site. */
    private function pageUrl(Request $request): ?string
    {
        $referer = (string) $request->headers->get('referer', '');
        $host = parse_url($referer, PHP_URL_HOST);

        return is_string($host) && strcasecmp($host, $request->getHost()) === 0 ? $referer : null;
    }

    private function clip(?string $value, int $length): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === null || $value === '' ? null : Str::limit($value, $length - 3);
    }
}
