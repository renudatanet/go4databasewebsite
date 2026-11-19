{{-- Page Manage tab bar: drawn by the admin layout above any screen that
     edits one of the site's pages (PageManageTabsComposer). Each tab is one
     of the page's existing screens. --}}
@if(!empty($pm_page))
<style>
    .g4pm-bar{margin:24px 15px 0;background:#fff;border:1px solid #e6e9f0;border-radius:6px;box-shadow:0 1px 2px rgba(16,24,40,.04)}
    .g4pm-bar__head{display:flex;flex-wrap:wrap;align-items:center;gap:6px 14px;padding:14px 20px 0}
    .g4pm-bar__back{font-size:13px;color:#6b7280}
    .g4pm-bar__back:hover{color:#111827;text-decoration:none}
    .g4pm-bar__title{font-size:18px;font-weight:600;color:#111827;margin:0}
    .g4pm-bar__open{font-size:13px;color:#2563eb;margin-left:auto;white-space:nowrap}
    .g4pm-bar__open i{font-size:11px}
    .g4pm-tabs{display:flex;gap:4px;overflow-x:auto;padding:10px 14px 0;scrollbar-width:thin}
    .g4pm-tabs a{flex:0 0 auto;padding:9px 12px 11px;font-size:14px;color:#4b5563;border-bottom:2px solid transparent;white-space:nowrap}
    .g4pm-tabs a:hover{color:#111827;text-decoration:none}
    .g4pm-tabs a.is-active{color:#2563eb;border-bottom-color:#2563eb;font-weight:600}
</style>
<div class="g4pm-bar" role="navigation" aria-label="{{ __('Edit :page', ['page' => __($pm_page->title)]) }}">
    <div class="g4pm-bar__head">
        <a class="g4pm-bar__back" href="{{ route('admin.page.manage') }}"><i class="ti-arrow-left"></i> {{ __('All pages') }}</a>
        <h2 class="g4pm-bar__title">{{ __($pm_page->title) }}</h2>
        @if($pm_url)
            <a class="g4pm-bar__open" href="{{ $pm_url }}" target="_blank" rel="noopener">{{ '/' . ltrim((string) parse_url($pm_url, PHP_URL_PATH), '/') }} <i class="ti-new-window"></i></a>
        @endif
    </div>
    <nav class="g4pm-tabs">
        @foreach($pm_tabs as $tab)
            <a href="{{ $tab['url'] }}" @class(['is-active' => $tab['active']]) @if($tab['active']) aria-current="page" @endif>{{ $tab['label'] }}</a>
        @endforeach
    </nav>
</div>
@endif
