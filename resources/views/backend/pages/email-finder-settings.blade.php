@extends('backend.admin-master')
@section('site-title')
    {{__('Email Finder Settings')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                @include('backend.partials.message')
                <x-error-msg/>
            </div>

            <div class="col-lg-8 mt-5">
                <form action="{{route('admin.email.finder.settings')}}" method="POST">
                    @csrf

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title">{{__('How The Lookup Behaves')}}</h4>
                            <p class="text-muted">
                                {{__('These two numbers trade speed against thoroughness. Raising them finds a few more addresses on stubborn domains; lowering them makes every lookup faster. The page is usually answered within the first two tries, so the defaults suit almost every case.')}}
                            </p>

                            <div class="alert alert-info">
                                {{__('The mail identity used for these checks (sender domain, sender address, timeout, and whether live checks run at all) is shared with the Email Verifier and is set on the Email Verifier Settings screen. There is one identity for the whole site on purpose, so the two tools cannot drift apart.')}}
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email_finder_max_probes">{{__('Maximum Addresses To Try')}}</label>
                                        <input type="number" min="1" max="10" name="email_finder_max_probes"
                                               id="email_finder_max_probes" class="form-control"
                                               value="{{get_static_option('email_finder_max_probes') ?: 6}}">
                                        <small class="text-muted">{{__('How many possible address formats to test before giving up and showing the most likely one as a guess. Default 6.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email_finder_unknown_bail">{{__('Stop After This Many No-Answers')}}</label>
                                        <input type="number" min="1" max="10" name="email_finder_unknown_bail"
                                               id="email_finder_unknown_bail" class="form-control"
                                               value="{{get_static_option('email_finder_unknown_bail') ?: 2}}">
                                        <small class="text-muted">{{__('Some large mail servers refuse to answer any address check. Once this many tries come back with no answer, the rest will too, so we stop rather than keep the visitor waiting. Default 2.')}}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_finder_enable_catchall_check"
                                           name="email_finder_enable_catchall_check"
                                           @if(get_static_option('email_finder_enable_catchall_check', '1') == '1') checked @endif>
                                    <label class="custom-control-label" for="email_finder_enable_catchall_check">{{__('Detect catch-all domains')}}</label>
                                </div>
                                <small class="text-muted">
                                    {{__('Some companies accept mail at every address on their domain, real or not. This check spots them, so the page can say the result is unconfirmed instead of claiming an address was verified. Leave this on: turning it off makes lookups slightly faster but lets the page report guesses as confirmed hits.')}}
                                </small>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{__('Save Settings')}}</button>
                </form>
            </div>

            <div class="col-lg-4 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="header-title">{{__('The Page Itself')}}</h5>
                        <p class="text-muted">
                            {{__('Everything the visitor reads on the Email Finder page, the headline, the steps, the format table and the buttons, is edited on the Email Finder Page Content screen.')}}
                        </p>
                        <a href="{{route('admin.email.finder.content')}}" class="btn btn-outline-primary btn-sm">
                            {{__('Edit Page Content')}}
                        </a>
                        <hr>
                        <a href="{{url('/email-finder')}}" target="_blank" rel="noopener">{{__('View the live page')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
