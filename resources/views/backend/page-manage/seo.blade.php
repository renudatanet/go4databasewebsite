@extends('backend.admin-master')
@php $meta_only = $site_page->seoKey === null; @endphp
@section('site-title')
    {{ $meta_only ? __(':page: Meta title & description', ['page' => __($site_page->title)]) : __(':page: Address & SEO', ['page' => __($site_page->title)]) }}
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/media-uploader.css') }}">
    <style>
        .g4pm-slug .input-group-text{background:#f9fafb;color:#6b7280}
        .g4pm-count{font-size:12px;color:#6b7280}
        .g4pm-count.is-over{color:#dc2626}
    </style>
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-12 mt-4">
                @include('backend.partials.message')
                <x-error-msg/>
                <form action="{{ route('admin.page.manage.seo', $site_page->key) }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">{{ __('Address') }}</h4>
                            @if($site_page->slugEditable)
                                <div class="form-group g4pm-slug mb-1">
                                    <label for="slug" class="sr-only">{{ __('Address') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">{{ rtrim(url('/'), '/') }}/</span></div>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $slug) }}" required maxlength="191" autocomplete="off">
                                    </div>
                                    @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <small class="text-muted">{{ __('Lower-case words joined by hyphens. Changing it moves the page: links to the old address, in menus or on Google, stop working, so update the menu and resubmit the sitemap afterwards.') }}</small>
                            @else
                                <p class="mb-0">
                                    <a href="{{ $public_url }}" target="_blank" rel="noopener">{{ $public_url }}</a>
                                    <br><small class="text-muted">{{ __('This page\'s address is fixed by the site and can\'t be changed here.') }}</small>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body">
                            <h4 class="header-title">{{ $meta_only ? __('Meta title & description') : __('Name & SEO') }}</h4>
                            <p class="text-muted">{{ $meta_only
                                ? __('What Google and the browser tab show for this page. The meta title is used exactly as you type it; leave a box empty to keep the page\'s own wording.')
                                : __('What the page is called, and what search engines and shared links show. A box left empty uses the page\'s own wording.') }}</p>
                            @if($languages->count() > 1)
                                <ul class="nav nav-tabs" role="tablist">
                                    @foreach($languages as $i => $lang)
                                        <li class="nav-item"><a class="nav-link @if($i === 0) active @endif" data-toggle="tab" href="#seo-{{ $lang->slug }}" role="tab">{{ $lang->name }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="tab-content @if($languages->count() > 1) margin-top-30 @endif">
                                @foreach($languages as $i => $lang)
                                    @php $v = $values[$lang->slug] ?? []; $f = fn ($field) => "seo[{$lang->slug}][{$field}]"; @endphp
                                    <div class="tab-pane fade @if($i === 0) show active @endif" id="seo-{{ $lang->slug }}" role="tabpanel">
                                        @if(in_array('name', $fields))
                                        <div class="form-group">
                                            <label for="name-{{ $lang->slug }}">{{ __('Page name') }}</label>
                                            <input type="text" class="form-control" id="name-{{ $lang->slug }}" name="{{ $f('name') }}" value="{{ old("seo.{$lang->slug}.name", $v['name'] ?? '') }}" maxlength="191">
                                            <small class="text-muted">{{ __('Used in the browser tab and the breadcrumb.') }}</small>
                                        </div>
                                        @endif
                                        @if(in_array('meta_tags', $fields))
                                        <div class="form-group">
                                            <label for="title-{{ $lang->slug }}">{{ __('Meta title') }}</label>
                                            <input type="text" class="form-control" id="title-{{ $lang->slug }}" name="{{ $f('meta_tags') }}" value="{{ old("seo.{$lang->slug}.meta_tags", $v['meta_tags'] ?? '') }}" maxlength="191" data-g4pm-count="60">
                                            <span class="g4pm-count"></span>
                                        </div>
                                        @endif
                                        @if(in_array('meta_description', $fields))
                                        <div class="form-group">
                                            <label for="desc-{{ $lang->slug }}">{{ __('Meta description') }}</label>
                                            <textarea class="form-control" id="desc-{{ $lang->slug }}" name="{{ $f('meta_description') }}" rows="3" maxlength="500" data-g4pm-count="160">{{ old("seo.{$lang->slug}.meta_description", $v['meta_description'] ?? '') }}</textarea>
                                            <span class="g4pm-count"></span>
                                        </div>
                                        @endif
                                        @if(in_array('schema_code', $fields))
                                        <div class="form-group">
                                            <label for="schema-{{ $lang->slug }}">{{ __('Schema (JSON-LD)') }}</label>
                                            <textarea class="form-control" id="schema-{{ $lang->slug }}" name="{{ $f('schema_code') }}" rows="4">{{ old("seo.{$lang->slug}.schema_code", $v['schema_code'] ?? '') }}</textarea>
                                        </div>
                                        @endif
                                        @if(in_array('meta_image', $fields))
                                        <x-media-upload :title="__('Share image (Open Graph)')" :name="$f('meta_image')" :id="(string) ($v['meta_image'] ?? '')" :dimentions="'1200x630'"/>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4">{{ $meta_only ? __('Save meta title & description') : __('Save address & SEO') }}</button>
                </form>
            </div>
        </div>
    </div>
    @include('backend.partials.media-upload.media-upload-markup')
@endsection
@section('script')
    <script>
        (function () {
            document.querySelectorAll('[data-g4pm-count]').forEach(function (box) {
                var max = parseInt(box.getAttribute('data-g4pm-count'), 10);
                var out = box.parentNode.querySelector('.g4pm-count');
                var show = function () {
                    var n = box.value.length;
                    out.textContent = n + ' / ' + max + (n > max ? ' — {{ __('search engines will cut it short') }}' : '');
                    out.classList.toggle('is-over', n > max);
                };
                box.addEventListener('input', show);
                show();
            });
        })();
    </script>
    <script src="{{ asset('assets/backend/js/dropzone.js') }}"></script>
    @include('backend.partials.media-upload.media-js')
@endsection
