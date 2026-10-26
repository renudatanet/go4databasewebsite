{{-- Sharing tags (Open Graph, Twitter) for the blog listings, which had none:
     a shared link showed no title, description or picture. The picture is
     the one set in admin, else the newest post's. Passed in: $share_title,
     $share_description, optionally $share_image (a URL). --}}
@php
    $share_image = $share_image ?? '';
    if (empty($share_image) && $all_blogs->count()) {
        $share_image = get_attachment_image_url($all_blogs->first()->image, 'large');
    }
    $site_name = get_static_option('site_' . $user_select_lang_slug . '_title');
    $share_title = trim(preg_replace('/\s+/', ' ', (string) $share_title));
@endphp
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $share_title }}">
@if(!empty($share_description))
    <meta property="og:description" content="{{ $share_description }}">
@endif
@if(!empty($site_name))
    <meta property="og:site_name" content="{{ $site_name }}">
@endif
@if(!empty($share_image))
    <meta property="og:image" content="{{ $share_image }}">
@endif
<meta name="twitter:card" content="{{ $share_image ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $share_title }}">
@if(!empty($share_description))
    <meta name="twitter:description" content="{{ $share_description }}">
@endif
@if(!empty($share_image))
    <meta name="twitter:image" content="{{ $share_image }}">
@endif
