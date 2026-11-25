@extends('backend.admin-master')
@section('site-title')
    {{ __('Homepage content') }}
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/media-uploader.css') }}">
    <style>
        .g4hc-jump{display:flex;flex-wrap:wrap;gap:6px;margin:4px 0 0}
        .g4hc-jump a{font-size:12.5px;padding:4px 10px;border:1px solid #e6e9f0;border-radius:999px;color:#374151;background:#fff}
        .g4hc-jump a:hover{border-color:#2563eb;color:#2563eb;text-decoration:none}
        .g4hc-section{scroll-margin-top:90px}
        .g4hc-section .card-body{padding-bottom:8px}
        .g4hc-section h5{font-size:16px;font-weight:600;margin:0 0 4px}
        .g4hc-lines{font-size:13px;line-height:1.55;white-space:pre}
        .g4hc-help{display:block;color:#7b8794;font-size:12.5px;margin-top:4px}
        .g4hc-switch{display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f8faf9;border:1px solid #e6e9f0;border-radius:8px}
        .g4hc-switch input{width:18px;height:18px}
        .g4hc-save{position:sticky;bottom:0;z-index:5;background:linear-gradient(180deg,rgba(241,245,249,0),#f1f5f9 30%);padding:18px 0 14px}
    </style>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-4">
                <x-flash-msg/>
                <x-error-msg/>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:10px">
                            <div>
                                <h4 class="header-title mb-1">{{ __('Homepage content') }}</h4>
                                <p class="text-muted mb-2">{{ __('Every heading, text, button, link and list on the homepage, top to bottom. Each field shows what the page says now; empty a field to go back to the built-in wording. The latest news, contact area and site title are on their own tabs above.') }}</p>
                            </div>
                            <a class="btn btn-primary" href="{{ url('/') }}" target="_blank" rel="noopener">{{ __('View homepage') }}</a>
                        </div>
                        <div class="g4hc-jump" aria-label="{{ __('Sections') }}">
                            @foreach($sections as $title => $fields)
                                <a href="#g4hc-{{ \Illuminate\Support\Str::slug($title) }}">{{ __($title) }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.home.content') }}" method="POST">
                    @csrf
                    @if($languages->count() > 1)
                        <ul class="nav nav-tabs mt-4" role="tablist">
                            @foreach($languages as $i => $lang)
                                <li class="nav-item"><a class="nav-link @if($i === 0) active @endif" data-toggle="tab" href="#g4hc-lang-{{ $lang->slug }}" role="tab">{{ $lang->name }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="tab-content">
                        @foreach($languages as $i => $lang)
                            @php $text = new \App\HomePage\HomePageText($lang->slug); @endphp
                            <div class="tab-pane fade @if($i === 0) show active @endif" id="g4hc-lang-{{ $lang->slug }}" role="tabpanel">
                                @foreach($sections as $title => $fields)
                                    <div class="card mt-4 g4hc-section" id="{{ $i === 0 ? 'g4hc-' . \Illuminate\Support\Str::slug($title) : '' }}">
                                        <div class="card-body">
                                            <h5>{{ __($title) }}</h5>
                                            <div class="row mt-3">
                                                @foreach($fields as $key => [$label, $type, $help])
                                                    @php $name = \App\HomePage\HomePageText::optionName($lang->slug, $key); $value = old($name, $text->get($key)); @endphp
                                                    <div class="form-group {{ in_array($type, ['input', 'url'], true) ? 'col-md-6' : 'col-12' }}">
                                                        @if($type === 'switch')
                                                            <label class="g4hc-switch mb-0">
                                                                <input type="hidden" name="{{ $name }}" value="0">
                                                                <input type="checkbox" name="{{ $name }}" value="1" @checked($value !== '0')>
                                                                <span>{{ __($label) }}</span>
                                                            </label>
                                                        @elseif($type === 'image')
                                                            <x-media-upload :title="__($label)" :name="$name" :id="(string) $text->get($key)" :dimentions="'1200x900'"/>
                                                        @else
                                                            <label for="{{ $name }}">{{ __($label) }}</label>
                                                            @if($type === 'lines' || $type === 'textarea')
                                                                <textarea class="form-control {{ $type === 'lines' ? 'g4hc-lines' : '' }}" id="{{ $name }}" name="{{ $name }}" rows="{{ $type === 'lines' ? max(3, min(10, substr_count($value, "\n") + 2)) : 3 }}" placeholder="{{ $defaults[$key] ?? '' }}">{{ $value }}</textarea>
                                                            @else
                                                                <input type="text" class="form-control" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $defaults[$key] ?? '' }}">
                                                            @endif
                                                        @endif
                                                        @if($help !== '')<small class="g4hc-help">{{ __($help) }}</small>@endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="g4hc-save">
                        <button type="submit" class="btn btn-primary pl-4 pr-4">{{ __('Save homepage content') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('backend.partials.media-upload.media-upload-markup')
@endsection
@section('script')
    <script src="{{ asset('assets/backend/js/dropzone.js') }}"></script>
    @include('backend.partials.media-upload.media-js')
@endsection
