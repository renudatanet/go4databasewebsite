@extends('frontend.frontend-page-master')
@section('site-title')
    {{get_static_option('faq_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-title')
    {{get_static_option('faq_page_'.$user_select_lang_slug.'_name')}}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{get_static_option('faq_page_'.$user_select_lang_slug.'_meta_description')}}">
    <meta name="tags" content="{{get_static_option('faq_page_'.$user_select_lang_slug.'_meta_tags')}}">
    {!! render_og_meta_image_by_attachment_id(get_static_option('faq_page_'.$user_select_lang_slug.'_meta_image')) !!}
@endsection
@push('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('assets/frontend/css/faq.css')}}">
@endpush

@section('content')
<div class="faq-page-wrap">
  <div class="faq-page-inner">

    <div class="faq-page-header">
      <h1 class="faq-page-title">Frequently Asked <span class="highlight">Questions</span></h1>
      <p class="faq-page-subtitle">Everything you need to know about your account, our data, orders, billing, and privacy — organized by topic so you can find the answer fast.</p>

      @if(($all_categories ?? collect())->count() > 1)
        <div class="faq-jump-nav">
          @foreach($all_categories as $category)
            <a href="#faq-section-{{$category->id}}" class="faq-jump-link">{{$category->name}}</a>
          @endforeach
          @if(($uncategorized_faqs ?? collect())->isNotEmpty())
            <a href="#faq-section-general" class="faq-jump-link">General</a>
          @endif
        </div>
      @endif
    </div>

    @forelse($all_categories as $category)
      <div class="faq-section-group" id="faq-section-{{$category->id}}">
        <div class="faq-section-head">
          <span class="faq-section-icon">{{$loop->iteration}}</span>
          <span class="faq-section-title">{{$category->name}}</span>
          <span class="faq-section-count">{{$category->faqs->count()}} {{$category->faqs->count() == 1 ? 'question' : 'questions'}}</span>
        </div>
        <div class="faq-accordion">
          @foreach($category->faqs as $data)
            <div class="card faq-item @if($data->is_open == 'on') active @endif" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
              <button type="button" class="faq-question" itemprop="name">
                <span>{{$data->title}}</span>
                <i data-lucide="chevron-down" class="faq-arrow"></i>
              </button>
              <div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" @if($data->is_open == 'on') style="max-height:600px" @endif>
                <div itemprop="text">{!! $data->description !!}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
    @endforelse

    @if(($uncategorized_faqs ?? collect())->isNotEmpty())
      <div class="faq-section-group" id="faq-section-general">
        <div class="faq-section-head">
          <span class="faq-section-icon">{{ ($all_categories ?? collect())->count() + 1 }}</span>
          <span class="faq-section-title">General</span>
          <span class="faq-section-count">{{$uncategorized_faqs->count()}} {{$uncategorized_faqs->count() == 1 ? 'question' : 'questions'}}</span>
        </div>
        <div class="faq-accordion">
          @foreach($uncategorized_faqs as $data)
            <div class="card faq-item @if($data->is_open == 'on') active @endif" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
              <button type="button" class="faq-question" itemprop="name">
                <span>{{$data->title}}</span>
                <i data-lucide="chevron-down" class="faq-arrow"></i>
              </button>
              <div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" @if($data->is_open == 'on') style="max-height:600px" @endif>
                <div itemprop="text">{!! $data->description !!}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

  </div>
</div>
@endsection

@push('script')
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="{{asset('assets/frontend/js/faq.js')}}"></script>
@endpush
