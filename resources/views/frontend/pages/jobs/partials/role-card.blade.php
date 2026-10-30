{{-- One open role in a list: its team, title and facts, and when
     applications close. The title's link covers the whole card. --}}
@php
    use App\Careers\Icon;
    $team = $job_board->categoryOf($job);
    $closes = $job->closesAt();
    $days_left = $closes ? (int) now()->startOfDay()->diffInDays($closes, false) : null;
@endphp
<li>
    <article class="g4c-role">
        <div>
            @if($team)<span class="g4c-role-team">{{ $team->title }}</span>@endif
            <h3 class="g4c-role-title"><a href="{{ route('frontend.jobs.single', $job->slug) }}">{{ $job->title }}</a></h3>
            <ul class="g4c-facts">
                @if(trim((string) $job->job_location) !== '')<li>{!! Icon::svg('pin') !!}<span>{{ $job->job_location }}</span></li>@endif
                @if($job->employment_status)<li>{!! Icon::svg('clock') !!}<span>{{ $job->typeLabel() }}</span></li>@endif
                @if(trim((string) $job->salary) !== '')<li>{!! Icon::svg('wallet') !!}<span>{{ $job->salary }}</span></li>@endif
            </ul>
        </div>
        <div class="g4c-role-side">
            @if($closes)
                @if($days_left !== null && $days_left <= 7)
                    <span class="g4c-date is-soon">{{ $days_left <= 0 ? __('Closes today') : __('Closes in') . ' ' . $days_left . ' ' . ($days_left === 1 ? __('day') : __('days')) }}</span>
                @else
                    <span class="g4c-date">{{ __('Apply by') }} {{ $closes->format('j M Y') }}</span>
                @endif
            @endif
            <span class="g4c-go" aria-hidden="true">{{ __('View role') }} {!! Icon::svg('arrow') !!}</span>
        </div>
    </article>
</li>
