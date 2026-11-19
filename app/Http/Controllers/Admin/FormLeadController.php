<?php

namespace App\Http\Controllers\Admin;

use App\FormLeads\FormKind;
use App\FormLeads\FormLead;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin > List > Form Leads: every submission of the list pages' forms, with
 * filters, a status per lead (new, contacted, done), delete and CSV export.
 */
class FormLeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        [$form, $status, $search] = $this->filters($request);

        return view('backend.form-leads.index', [
            'leads' => FormLead::query()->filtered($form, $status, $search)->latest('id')->paginate(25)->withQueryString(),
            'counts' => FormLead::query()->selectRaw('form, COUNT(*) AS n')->groupBy('form')->pluck('n', 'form'),
            'new_count' => FormLead::query()->where('status', 'new')->count(),
            'forms' => FormKind::options(),
            'statuses' => FormLead::STATUSES,
            'form' => $form,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function update_status(Request $request, int $id)
    {
        $data = $request->validate(['status' => 'required|in:' . implode(',', array_keys(FormLead::STATUSES))]);
        FormLead::query()->findOrFail($id)->update(['status' => $data['status']]);

        return $request->expectsJson()
            ? response()->json(['status' => 'ok'])
            : back()->with(['msg' => __('Status saved.'), 'type' => 'success']);
    }

    public function destroy(int $id)
    {
        optional(FormLead::query()->find($id))->delete();

        return back()->with(['msg' => __('Lead deleted.'), 'type' => 'danger']);
    }

    /** The leads the screen's filters show, as a CSV file. */
    public function export(Request $request): StreamedResponse
    {
        [$form, $status, $search] = $this->filters($request);
        $query = FormLead::query()->filtered($form, $status, $search)->latest('id');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // so Excel reads the names as UTF-8
            fputcsv($out, ['Date', 'Form', 'Name', 'Email', 'Phone', 'Website / business', 'Message', 'List', 'Page', 'Status']);
            $query->chunk(500, function ($leads) use ($out) {
                foreach ($leads as $lead) {
                    fputcsv($out, array_map([$this, 'cell'], [
                        $lead->created_at?->format('Y-m-d H:i'),
                        $lead->kind()?->label() ?? $lead->form,
                        $lead->name, $lead->email, $lead->phone, $lead->company, $lead->message,
                        $lead->list_title, $lead->page_url,
                        FormLead::STATUSES[$lead->status] ?? $lead->status,
                    ]));
                }
            });
            fclose($out);
        }, 'form-leads-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** A spreadsheet must not run what a visitor typed as a formula. */
    public function cell($value): string
    {
        $value = (string) $value;
        if ($value === '' || preg_match('/^\+?[0-9 ().-]+$/', $value)) {
            return $value; // empty, or a phone number like +1 (555) 010-0199
        }

        return in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'" . $value : $value;
    }

    /** @return array{0: ?string, 1: ?string, 2: ?string} */
    private function filters(Request $request): array
    {
        $form = $request->query('form');
        $status = $request->query('status');

        return [
            is_string($form) && FormKind::tryFrom($form) ? $form : null,
            is_string($status) && array_key_exists($status, FormLead::STATUSES) ? $status : null,
            is_string($request->query('q')) ? trim($request->query('q')) : null,
        ];
    }
}
