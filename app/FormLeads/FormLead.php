<?php

namespace App\FormLeads;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** One submission of a list page form. */
class FormLead extends Model
{
    public const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'done' => 'Done'];

    protected $table = 'form_leads';

    protected $fillable = ['form', 'name', 'email', 'phone', 'company', 'message', 'list_id',
        'list_title', 'page_url', 'ip', 'user_agent', 'status', 'emailed'];

    protected $casts = ['emailed' => 'boolean', 'list_id' => 'integer'];

    public function kind(): ?FormKind
    {
        return FormKind::tryFrom((string) $this->form);
    }

    /**
     * The admin screen's filters: form, status, and a search over the
     * person's details, what they wrote and the list they were on.
     */
    public function scopeFiltered(Builder $query, ?string $form, ?string $status, ?string $search): Builder
    {
        if ($form !== null && FormKind::tryFrom($form) !== null) {
            $query->where('form', $form);
        }
        if ($status !== null && array_key_exists($status, self::STATUSES)) {
            $query->where('status', $status);
        }
        $search = trim((string) $search);
        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $query->where(function (Builder $q) use ($like) {
                foreach (['name', 'email', 'phone', 'company', 'message', 'list_title'] as $column) {
                    $q->orWhere($column, 'like', $like);
                }
            });
        }

        return $query;
    }
}
