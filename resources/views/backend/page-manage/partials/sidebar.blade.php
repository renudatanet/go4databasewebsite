{{-- The Page Manage group in the admin sidebar (PageManageSidebarComposer). --}}
@if(!empty($pm_links))
<li class="main_dropdown @if($pm_group_active) active @endif">
    <a href="javascript:void(0)" aria-expanded="true"><i class="ti-layout"></i>
        <span>{{ __('Page Manage') }}</span></a>
    <ul class="collapse">
        <li class="@if($pm_overview_active) active @endif"><a href="{{ route('admin.page.manage') }}">{{ __('All Pages') }}</a></li>
        @foreach($pm_links as $link)
            <li class="@if($link['active']) active @endif"><a href="{{ $link['url'] }}">{{ $link['title'] }}</a></li>
        @endforeach
    </ul>
</li>
@endif
