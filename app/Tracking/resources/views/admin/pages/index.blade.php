@extends('tracking::admin.layout')
@section('screen-title', 'Pages')
@section('screen')
@include('tracking::admin.partials.toolbar', ['context' => $context, 'keep' => ['q' => $search, 'section' => $section, 'sort' => $sort]])
@php $query = $context->toQuery(); @endphp
<section class="g4t-card">
    <div class="g4t-card-head">
        <h3>{{ $fmt::number($paginator->total()) }} pages viewed</h3>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}">Download CSV</a>
    </div>
    <form method="get" class="g4t-card-body" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center">
        @foreach($query as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach
        <input type="search" name="q" value="{{ $search }}" placeholder="Search path or title" aria-label="Search pages" class="form-control" style="max-width:280px;height:34px">
        <select name="section" class="form-control" style="max-width:200px;height:34px" aria-label="Section">
            <option value="">All sections</option>
            @foreach($sections as $name)
                <option value="{{ $name }}" {{ $section === $name ? 'selected' : '' }}>{{ $name === 'home' ? 'Homepage' : '/' . $name }}</option>
            @endforeach
        </select>
        <select name="sort" class="form-control" style="max-width:180px;height:34px" aria-label="Sort by">
            @foreach($sorts as $key => $name)
                <option value="{{ $key }}" {{ $sort === $key ? 'selected' : '' }}>Sort: {{ $name }}</option>
            @endforeach
        </select>
        <button class="g4t-btn is-quiet" type="submit">Show</button>
    </form>
    @if(empty($rows))
        <div class="g4t-empty"><strong>No pages match</strong>Try a longer period or clear the search.</div>
    @else
    <div class="g4t-table-wrap"><table>
        <thead><tr>
            <th>Page</th><th class="n">Views</th><th class="n">Visitors</th><th class="n">Engaged</th><th class="n">Scrolled</th>
            <th class="n">Entrances</th><th class="n">Exit rate</th><th class="n">Clicks</th><th class="n">Click-through</th>
        </tr></thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td><a class="g4t-strong" href="{{ route('admin.tracking.pages.show', ['page' => $row['page_id']] + $query) }}">{{ $fmt::path($row['path'], 70) }}</a><span class="g4t-sub">{{ $row['title'] }}</span></td>
                <td class="n">{{ $fmt::number($row['views']) }}</td>
                <td class="n">{{ $fmt::number($row['visitors']) }}</td>
                <td class="n">{{ $fmt::duration($row['avg_engaged_ms']) }}</td>
                <td class="n">{{ $row['avg_scroll'] }}%</td>
                <td class="n">{{ $fmt::number($row['entrances']) }}</td>
                <td class="n">{{ $fmt::percent($row['exit_rate']) }}</td>
                <td class="n">{{ $fmt::number($row['clicks']) }}</td>
                <td class="n">{{ $fmt::percent($row['ctr']) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $paginator->links('pagination::bootstrap-4') }}
    @endif
</section>
<p class="g4t-faint" style="margin-top:8px">Click-through: the share of a page's visitors who clicked something on it. Engaged: average time the page was visible and in use.</p>
@endsection
