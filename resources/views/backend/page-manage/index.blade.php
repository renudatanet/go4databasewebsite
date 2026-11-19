@extends('backend.admin-master')
@section('site-title')
    {{ __('Page Manage') }}
@endsection
@section('style')
<style>
    .g4pm-intro{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:20px}
    .g4pm-intro p{margin:4px 0 0;color:#6b7280;max-width:640px}
    .g4pm-search{max-width:280px}
    .g4pm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
    .g4pm-card{display:flex;flex-direction:column;background:#fff;border:1px solid #e6e9f0;border-radius:6px;padding:18px 18px 16px}
    .g4pm-card h3{font-size:16px;font-weight:600;margin:0;color:#111827}
    .g4pm-card__path{font-size:13px;color:#2563eb;word-break:break-all}
    .g4pm-card__note{font-size:13px;color:#6b7280;margin:8px 0 0}
    .g4pm-card__tabs{display:flex;flex-wrap:wrap;gap:6px;margin:14px 0 16px}
    .g4pm-card__tabs a{font-size:12px;padding:4px 9px;border:1px solid #e6e9f0;border-radius:999px;color:#374151;background:#f9fafb}
    .g4pm-card__tabs a:hover{border-color:#2563eb;color:#2563eb;text-decoration:none}
    .g4pm-card__foot{margin-top:auto;display:flex;gap:8px;align-items:center}
    .g4pm-card--custom{border-style:dashed}
    .g4pm-empty{display:none;color:#6b7280;padding:24px 0}
</style>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-5">
                @include('backend.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="g4pm-intro">
                            <div>
                                <h4 class="header-title mb-0">{{ __('Every page of the site') }}</h4>
                                <p>{{ __('Open a page to edit it. Everything you can change on that page is in the tabs at the top of its screen: its wording, its sections and settings, the records it shows, and its address & SEO.') }}</p>
                            </div>
                            <input type="search" class="form-control g4pm-search" id="g4pm-search" placeholder="{{ __('Find a page...') }}" aria-label="{{ __('Find a page') }}">
                        </div>

                        <div class="g4pm-grid" id="g4pm-grid">
                            @foreach($pages as $item)
                                @php $page = $item['page']; @endphp
                                <div class="g4pm-card" data-search="{{ \Illuminate\Support\Str::lower(__($page->title) . ' ' . ($item['url'] ?? '') . ' ' . collect($item['tabs'])->pluck('label')->implode(' ')) }}">
                                    <h3>{{ __($page->title) }}</h3>
                                    @if($item['url'])
                                        <a class="g4pm-card__path" href="{{ $item['url'] }}" target="_blank" rel="noopener">{{ '/' . ltrim((string) parse_url($item['url'], PHP_URL_PATH), '/') }} <i class="ti-new-window"></i></a>
                                    @endif
                                    @if($page->note !== '')<p class="g4pm-card__note">{{ __($page->note) }}</p>@endif
                                    <div class="g4pm-card__tabs">
                                        @foreach($item['tabs'] as $tab)
                                            <a href="{{ $tab['url'] }}">{{ $tab['label'] }}</a>
                                        @endforeach
                                    </div>
                                    <div class="g4pm-card__foot">
                                        <a class="btn btn-primary btn-sm" href="{{ route('admin.page.manage.show', $page->key) }}">{{ __('Edit page') }}</a>
                                    </div>
                                </div>
                            @endforeach

                            @if($can_custom_pages)
                                <div class="g4pm-card g4pm-card--custom" data-search="{{ \Illuminate\Support\Str::lower(__('Custom pages') . ' privacy terms') }}">
                                    <h3>{{ __('Custom pages') }}</h3>
                                    <p class="g4pm-card__note">{{ __('Pages written in the page editor, such as the privacy policy and terms. Each has its own address and SEO in the editor.') }}</p>
                                    <div class="g4pm-card__foot mt-3">
                                        <a class="btn btn-primary btn-sm" href="{{ route('admin.page') }}">{{ __('All custom pages') }}</a>
                                        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.page.new') }}">{{ __('Add new page') }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <p class="g4pm-empty" id="g4pm-empty">{{ __('No page matches that search.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    (function () {
        var box = document.getElementById('g4pm-search');
        var cards = document.querySelectorAll('#g4pm-grid .g4pm-card');
        var empty = document.getElementById('g4pm-empty');
        if (!box) { return; }
        box.addEventListener('input', function () {
            var q = box.value.trim().toLowerCase(), shown = 0;
            cards.forEach(function (card) {
                var hit = q === '' || card.getAttribute('data-search').indexOf(q) !== -1;
                card.style.display = hit ? '' : 'none';
                if (hit) { shown++; }
            });
            empty.style.display = shown ? 'none' : 'block';
        });
    })();
</script>
@endsection
