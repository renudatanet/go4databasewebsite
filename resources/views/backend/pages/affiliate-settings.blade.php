@extends('backend.admin-master')
@section('site-title')
    {{__('Affiliate Settings')}}
@endsection
@php
    $af = fn($k) => \App\Http\Controllers\AffiliateSettingsController::term($k);
    $rate_set = \App\Http\Controllers\AffiliateSettingsController::rate() !== null;
@endphp
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                @include('backend.partials.message')
                <x-error-msg/>
            </div>

            <div class="col-lg-8 mt-5">
                @if(!$rate_set)
                    <div class="alert alert-warning">
                        <strong>{{__('The commission rate is not set yet.')}}</strong>
                        {{__('Until it is, the Affiliate page shows "To be confirmed" in place of every term and the earnings calculator will not quote a figure. That is deliberate: these are public promises, so the page will not invent one.')}}
                    </div>
                @endif

                <form action="{{route('admin.affiliate.settings')}}" method="POST">
                    @csrf

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title">{{__('Programme Terms')}}</h4>
                            <p class="text-muted">
                                {{__('These five values are what the page promises partners. Each one appears both in the terms row and, where relevant, in the earnings calculator. Leave one blank and the page says "To be confirmed" rather than guessing.')}}
                            </p>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_commission_rate">{{__('Commission Rate (%)')}}</label>
                                        <input type="number" step="0.1" min="0.1" max="100" name="affiliate_commission_rate"
                                               id="affiliate_commission_rate" class="form-control"
                                               placeholder="20" value="{{$af('commission_rate')}}">
                                        <small class="text-muted">{{__('Digits only, no % sign. This one also drives the calculator.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_referral_discount">{{__('Discount For Their Audience')}}</label>
                                        <input type="text" name="affiliate_referral_discount" id="affiliate_referral_discount"
                                               class="form-control" placeholder="25%" value="{{$af('referral_discount')}}">
                                        <small class="text-muted">{{__('What someone saves by using a partner link, for example 25%. This is NOT the commission: it is what the customer pays less. Leave blank and the discount strip is hidden entirely.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_commission_term">{{__('How Long They Earn')}}</label>
                                        <input type="text" name="affiliate_commission_term" id="affiliate_commission_term"
                                               class="form-control" placeholder="12 months" value="{{$af('commission_term')}}">
                                        <small class="text-muted">{{__('For example: 12 months, or Lifetime.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_cookie_days">{{__('Cookie Window')}}</label>
                                        <input type="text" name="affiliate_cookie_days" id="affiliate_cookie_days"
                                               class="form-control" placeholder="90 days" value="{{$af('cookie_days')}}">
                                        <small class="text-muted">{{__('How long after a click a signup still counts as theirs.')}}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_payout_method">{{__('Paid By')}}</label>
                                        <input type="text" name="affiliate_payout_method" id="affiliate_payout_method"
                                               class="form-control" placeholder="PayPal" value="{{$af('payout_method')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_payout_minimum">{{__('Minimum Payout')}}</label>
                                        <input type="text" name="affiliate_payout_minimum" id="affiliate_payout_minimum"
                                               class="form-control" placeholder="$50" value="{{$af('payout_minimum')}}">
                                        <small class="text-muted">{{__('Include the currency symbol.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_apply_url">{{__('Apply Button Link')}}</label>
                                        <input type="text" name="affiliate_apply_url" id="affiliate_apply_url"
                                               class="form-control" placeholder="https://app.go4database.com/affiliate/signup"
                                               value="{{$af('apply_url')}}">
                                        <small class="text-muted">{{__('Where every Apply button goes. Leave blank and the buttons scroll to the application form at the bottom of the page.')}}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="affiliate_notify_email">{{__('Email Applications To')}}</label>
                                <input type="email" name="affiliate_notify_email" id="affiliate_notify_email"
                                       class="form-control" style="max-width:420px"
                                       placeholder="success@go4database.com" value="{{$af('notify_email')}}">
                                <small class="text-muted">
                                    {{__('Who gets an email when someone applies. Leave blank and applications are only visible in Affiliate Applications. Applications are always saved first, so a mail problem can never lose one.')}}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title">{{__('Earnings Calculator')}}</h4>
                            <p class="text-muted">{{__('Controls the sliders on the page. The commission rate above is what it multiplies by.')}}</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="affiliate_plan_values">{{__('Plan Prices To Offer')}}</label>
                                        <input type="text" name="affiliate_plan_values" id="affiliate_plan_values"
                                               class="form-control" placeholder="49,99,199" value="{{$af('plan_values')}}">
                                        <small class="text-muted">{{__('Comma separated, up to four. These become the plan buttons a visitor picks from, so use prices that match your real plans.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="affiliate_max_referrals">{{__('Slider Maximum')}}</label>
                                        <input type="number" min="5" max="500" name="affiliate_max_referrals"
                                               id="affiliate_max_referrals" class="form-control"
                                               placeholder="50" value="{{$af('max_referrals')}}">
                                        <small class="text-muted">{{__('The highest number of referrals per month the slider goes up to.')}}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_default_referrals">{{__('Slider Starting Value')}}</label>
                                        <input type="number" min="1" max="500" name="affiliate_default_referrals"
                                               id="affiliate_default_referrals" class="form-control"
                                               placeholder="10" value="{{$af('default_referrals')}}">
                                        <small class="text-muted">{{__('What the slider shows before anyone touches it.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_default_plan">{{__('Plan Selected First')}}</label>
                                        <input type="number" min="1" name="affiliate_default_plan"
                                               id="affiliate_default_plan" class="form-control"
                                               placeholder="99" value="{{$af('default_plan')}}">
                                        <small class="text-muted">{{__('Must be one of the plan prices above. Leave blank for the middle one.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="affiliate_calc_months">{{__('Months To Project')}}</label>
                                        <input type="number" min="1" max="60" name="affiliate_calc_months"
                                               id="affiliate_calc_months" class="form-control"
                                               placeholder="12" value="{{$af('calc_months')}}">
                                        <small class="text-muted">
                                            <strong>{{__('Keep this the same as the commission term above.')}}</strong>
                                            {{__('If partners are paid for 12 months, projecting 24 would quote earnings they never receive.')}}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title">{{__('Sections To Show')}}</h4>
                            <p class="text-muted">{{__('Switch any part of the page off without deleting its content. Everything stays edited and comes back when you switch it on again.')}}</p>

                            <div class="row">
                                @foreach(\App\Http\Controllers\AffiliateSettingsController::section_defaults() as $key => $label)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="affiliate_show_{{$key}}" name="affiliate_show_{{$key}}"
                                                       @if(\App\Http\Controllers\AffiliateSettingsController::show($key)) checked @endif>
                                                <label class="custom-control-label" for="affiliate_show_{{$key}}">{{__($label)}}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-secondary mb-0">
                                <small>
                                    {{__('The hero is always shown, since a page needs a headline. The discount strip also hides itself automatically whenever no discount is set, whatever this switch says.')}}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title">{{__('Testimonials')}}</h4>
                            <div class="form-group">
                                <label for="affiliate_testimonial_count">{{__('How Many To Show')}}</label>
                                <input type="number" min="1" max="12" name="affiliate_testimonial_count"
                                       id="affiliate_testimonial_count" class="form-control" style="max-width:220px"
                                       placeholder="3" value="{{$af('testimonial_count')}}">
                                <small class="text-muted">{{__('Taken from the Testimonial section in the sidebar, newest first. If none are published the strip is hidden.')}}</small>
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
                            {{__('Every heading, paragraph and button label on the Affiliate page, plus the three steps, the comparison table and the FAQ, is edited on the Affiliate Page Content screen.')}}
                        </p>
                        <a href="{{route('admin.affiliate.content')}}" class="btn btn-outline-primary btn-sm">
                            {{__('Edit Page Content')}}
                        </a>
                        <hr>
                        <a href="{{url('/affiliate')}}" target="_blank" rel="noopener">{{__('View the live page')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
