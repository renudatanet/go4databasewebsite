@extends('backend.admin-master')
@section('site-title')
    {{__('Chat Widget Settings')}}
@endsection
@php
    $cw = fn($k) => \App\Http\Controllers\ChatWidgetSettingsController::value($k);
@endphp
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                @include('backend.partials.message')
                <x-error-msg/>
            </div>

            <div class="col-lg-8 mt-5">
                <form action="{{route('admin.chat.widget.settings')}}" method="POST">
                    @csrf

                    {{-- ------------------------------------------------------
                         CONNECTION
                    ------------------------------------------------------ --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title">{{__('Chat Server Connection')}}</h4>
                            <p class="text-muted">{{__('The chat bubble on the website posts to this site first, and this site forwards the message to your chat server. Because the browser only ever talks to go4database.com, there is no cross-domain blocking to deal with and your API key never appears in the page source.')}}</p>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="chat_widget_status"
                                           name="chat_widget_status" @if($cw('status') === '1') checked @endif>
                                    <label class="custom-control-label" for="chat_widget_status">{{__('Show the chat button on the website')}}</label>
                                </div>
                                <small class="text-muted">{{__('Leave this off until the API URL below is filled in and the test comes back green.')}}</small>
                            </div>

                            <div class="form-group">
                                <label for="chat_widget_api_url">{{__('Chat API URL')}}</label>
                                <input type="text" name="chat_widget_api_url" id="chat_widget_api_url" class="form-control"
                                       placeholder="https://your-server.in/api/chat/message"
                                       value="{{$cw('api_url')}}">
                                <small class="text-muted">{{__('The full address on your other server that receives a visitor message and returns the reply. It is called with a POST request carrying JSON. It must start with https, a plain http address redirects and the message is lost on the way.')}}</small>
                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="chat_widget_auth_header">{{__('Auth Header Name')}}</label>
                                        <input type="text" name="chat_widget_auth_header" class="form-control"
                                               placeholder="X-API-Key" value="{{$cw('auth_header')}}">
                                        <small class="text-muted">{{__('Leave as X-API-Key. Do not use Authorization, the chat API reads that header as a customer login.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="chat_widget_auth_value">{{__('Auth Header Value / API Key')}}</label>
                                        <input type="text" name="chat_widget_auth_value" class="form-control"
                                               placeholder="Bearer xxxxxxxxxxxx" value="{{$cw('auth_value')}}">
                                        <small class="text-muted">{{__('Leave blank if your API needs no key. This is stored on the server and is never sent to the visitor\'s browser.')}}</small>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="mt-3">{{__('Field Names')}}</h5>
                            <p class="text-muted">{{__('Only change these if your API names its fields differently. The defaults suit most chat backends.')}}</p>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="chat_widget_message_field">{{__('Message Field')}}</label>
                                        <input type="text" name="chat_widget_message_field" class="form-control"
                                               placeholder="message" value="{{$cw('message_field')}}">
                                        <small class="text-muted">{{__('Field the visitor\'s text is sent in.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="chat_widget_session_field">{{__('Session Field')}}</label>
                                        <input type="text" name="chat_widget_session_field" class="form-control"
                                               placeholder="session_id" value="{{$cw('session_field')}}">
                                        <small class="text-muted">{{__('Field the conversation id is sent in.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="chat_widget_reply_path">{{__('Reply Field Path')}}</label>
                                        <input type="text" name="chat_widget_reply_path" class="form-control"
                                               placeholder="{{__('auto')}}" value="{{$cw('reply_path')}}">
                                        <small class="text-muted">{{__('Leave blank to detect it. Use dots for nesting, e.g. data.reply')}}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="chat_widget_extra_payload">{{__('Extra Request Fields (JSON)')}}</label>
                                <textarea name="chat_widget_extra_payload" class="form-control" rows="2"
                                          placeholder='{"bot_id":"1","source":"website"}'>{{$cw('extra_payload')}}</textarea>
                                <small class="text-muted">{{__('Optional. Anything here is merged into every request, for cases where your API needs a bot id or a source tag.')}}</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chat_widget_timeout">{{__('Request Timeout (seconds)')}}</label>
                                        <input type="number" min="3" max="120" name="chat_widget_timeout" class="form-control"
                                               value="{{$cw('timeout')}}">
                                        <small class="text-muted">{{__('How long to wait for your chat server before giving up.')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-4 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="chat_widget_send_page_url"
                                                   name="chat_widget_send_page_url" @if($cw('send_page_url') === '1') checked @endif>
                                            <label class="custom-control-label" for="chat_widget_send_page_url">{{__('Send the page URL with each message')}}</label>
                                        </div>
                                        <small class="text-muted">{{__('Lets you see which page the visitor was on. Turn off if your API rejects unknown fields.')}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ------------------------------------------------------
                         APPEARANCE
                    ------------------------------------------------------ --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="header-title">{{__('What The Visitor Sees')}}</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chat_widget_title">{{__('Chat Window Title')}}</label>
                                        <input type="text" name="chat_widget_title" class="form-control" value="{{$cw('title')}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="chat_widget_subtitle">{{__('Subtitle')}}</label>
                                        <input type="text" name="chat_widget_subtitle" class="form-control" value="{{$cw('subtitle')}}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="chat_widget_greeting">{{__('Opening Message')}}</label>
                                <textarea name="chat_widget_greeting" class="form-control" rows="2">{{$cw('greeting')}}</textarea>
                                <small class="text-muted">{{__('Shown before the visitor types anything. It is written by the website, not sent to your chat server.')}}</small>
                            </div>

                            <div class="form-group">
                                <label for="chat_widget_offline_text">{{__('Message When Chat Is Unreachable')}}</label>
                                <textarea name="chat_widget_offline_text" class="form-control" rows="2">{{$cw('offline_text')}}</textarea>
                                <small class="text-muted">{{__('Shown if your chat server does not answer, so the visitor is never left staring at nothing.')}}</small>
                            </div>

                            <div class="form-group">
                                <label for="chat_widget_busy_text">{{__('Message When Sending Too Fast')}}</label>
                                <textarea name="chat_widget_busy_text" class="form-control" rows="2">{{$cw('busy_text')}}</textarea>
                                <small class="text-muted">{{__('The chat API allows 10 messages a minute per conversation. This is shown when one visitor goes past that.')}}</small>
                            </div>

                            <div class="form-group">
                                <label for="chat_widget_human_note">{{__('Note When A Person Takes Over')}}</label>
                                <textarea name="chat_widget_human_note" class="form-control" rows="2">{{$cw('human_note')}}</textarea>
                                <small class="text-muted">{{__('Added under the reply when the chat API reports that the bot could not answer and your team has been alerted. Leave blank to show nothing.')}}</small>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="chat_widget_placeholder">{{__('Input Placeholder')}}</label>
                                        <input type="text" name="chat_widget_placeholder" class="form-control" value="{{$cw('placeholder')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="chat_widget_launcher_label">{{__('Button Tooltip')}}</label>
                                        <input type="text" name="chat_widget_launcher_label" class="form-control" value="{{$cw('launcher_label')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="chat_widget_color">{{__('Accent Colour')}}</label>
                                        <input type="text" name="chat_widget_color" class="form-control"
                                               placeholder="#6fd943" value="{{$cw('color')}}">
                                        <small class="text-muted">{{__('Defaults to the site green.')}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary pr-4 pl-4">{{__('Update Settings')}}</button>
                </form>

                {{-- ------------------------------------------------------
                     CONNECTION TEST
                ------------------------------------------------------ --}}
                <div class="card mt-4 mb-5">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Test The Connection')}}</h4>
                        <p class="text-muted">{{__('Saves nothing. Sends one throwaway message to the saved API URL and shows you exactly what came back, including which field held the reply. Save your settings first, then test.')}}</p>

                        <button type="button" class="btn btn-outline-primary" id="chat-widget-test">{{__('Send Test Message')}}</button>

                        <div id="chat-widget-test-out" class="mt-3" style="display:none">
                            <div id="chat-widget-test-summary" class="alert" role="alert"></div>
                            <pre style="background:#f6f8fa;border:1px solid #e7ecf1;border-radius:6px;padding:14px;font-size:12.5px;max-height:340px;overflow:auto;white-space:pre-wrap;word-break:break-word;" id="chat-widget-test-raw"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('chat-widget-test').addEventListener('click', function () {
            var btn = this,
                box = document.getElementById('chat-widget-test-out'),
                summary = document.getElementById('chat-widget-test-summary'),
                raw = document.getElementById('chat-widget-test-raw');

            btn.disabled = true;
            btn.textContent = '{{__('Testing...')}}';
            box.style.display = 'block';
            summary.className = 'alert alert-secondary';
            summary.textContent = '{{__('Contacting your chat server...')}}';
            raw.textContent = '';

            fetch('{{route('admin.chat.widget.settings.test')}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{csrf_token()}}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({api_url: document.getElementById('chat_widget_api_url').value})
            })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                summary.className = 'alert ' + (d.ok ? 'alert-success' : 'alert-warning');
                summary.textContent = d.summary || '';

                var lines = [];
                if (d.http_status) { lines.push('HTTP status: ' + d.http_status); }
                if (d.sent) { lines.push('We sent: ' + JSON.stringify(d.sent, null, 2)); }
                if (d.reply) { lines.push('Reply we read: ' + d.reply); }
                if (d.raw) { lines.push('Raw response: ' + d.raw); }
                raw.textContent = lines.join('\n\n');
            })
            .catch(function (e) {
                summary.className = 'alert alert-danger';
                summary.textContent = '{{__('The test request itself failed: ')}}' + e;
            })
            .finally(function () {
                btn.disabled = false;
                btn.textContent = '{{__('Send Test Message')}}';
            });
        });
    </script>
@endsection
