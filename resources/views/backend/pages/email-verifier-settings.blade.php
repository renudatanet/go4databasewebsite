@extends('backend.admin-master')
@section('site-title')
    {{__('Email Verifier Settings')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                @include('backend.partials.message')
                <x-error-msg/>
            </div>
            <div class="col-8 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__("Email Verifier Settings")}}</h4>
                        <p class="text-muted">{{__("Controls the live single-email checker on the public Email Verifier page. These fields set the identity your server presents when it connects to a recipient's mail server to check whether an address exists.")}}</p>
                        <form action="{{route('admin.email.verifier.settings')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="email_verifier_sender_domain">{{__('Sender / HELO Domain')}}</label>
                                <input type="text" name="email_verifier_sender_domain" class="form-control"
                                       placeholder="go4database.com"
                                       value="{{get_static_option('email_verifier_sender_domain')}}">
                                <small class="text-muted">{{__('The domain name your server introduces itself as during the check. Defaults to this site\'s domain if left blank.')}}</small>
                            </div>

                            <div class="form-group">
                                <label for="email_verifier_sender_email">{{__('Sender / MAIL FROM Address')}}</label>
                                <input type="email" name="email_verifier_sender_email" class="form-control"
                                       placeholder="verify@go4database.com"
                                       value="{{get_static_option('email_verifier_sender_email')}}">
                                <small class="text-muted">{{__('The address used as the sender during the check. No email is ever actually delivered, this is only used for the mail server handshake. Defaults to verify@ this site\'s domain if left blank.')}}</small>
                            </div>

                            <div class="form-group">
                                <label for="email_verifier_smtp_timeout">{{__('SMTP Connection Timeout (seconds)')}}</label>
                                <input type="number" min="2" max="30" name="email_verifier_smtp_timeout" class="form-control"
                                       value="{{get_static_option('email_verifier_smtp_timeout') ?: 8}}">
                                <small class="text-muted">{{__('How long to wait for a reply from the recipient\'s mail server before giving up and marking the result as Unknown.')}}</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_verifier_enable_smtp_check"
                                           name="email_verifier_enable_smtp_check"
                                           @if(get_static_option('email_verifier_enable_smtp_check', '1') == '1') checked @endif>
                                    <label class="custom-control-label" for="email_verifier_enable_smtp_check">{{__('Enable live mailbox check (SMTP)')}}</label>
                                </div>
                                <small class="text-muted">{{__('When on, we connect directly to the recipient\'s mail server to confirm the mailbox exists. Many hosting providers block this outbound connection (port 25), if checks always come back "Unknown", your host likely blocks it, turn this off to skip straight to syntax/domain/disposable checks only.')}}</small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_verifier_enable_catchall_check"
                                           name="email_verifier_enable_catchall_check"
                                           @if(get_static_option('email_verifier_enable_catchall_check', '1') == '1') checked @endif>
                                    <label class="custom-control-label" for="email_verifier_enable_catchall_check">{{__('Enable catch-all detection')}}</label>
                                </div>
                                <small class="text-muted">{{__('Runs one extra probe against a random address on the same domain to detect domains that accept mail for any address. Only applies when the live mailbox check above is on.')}}</small>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{__('Update Settings')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
