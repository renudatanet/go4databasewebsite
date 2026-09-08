@extends('frontend.frontend-page-master')
@section('og-meta')
<meta property="og:url" content="{{ route('frontend.services.single', [
    'service_slug' => $service_item->slug
]) }}"/>
    <meta property="og:type" content="article"/>
    <meta property="og:title" content="{{$service_item->title}}"/>
    {!! render_og_meta_image_by_attachment_id($service_item->image) !!}
@endsection
@section('page-meta-data')
    <meta name="description" content="{{$service_item->meta_description}}">
    <meta name="tags" content="{{$service_item->meta_tag}}">
    {!! render_og_meta_image_by_attachment_id($service_item->image) !!}
@endsection
@section('page-title')
    {{$service_item->title}}
@endsection
@php
use App\Services;

@endphp
@section('content')
<style>

    /*.container{*/
    /*  width:1280px;*/
    /*  margin:auto;*/
    /*  padding:30px 0 80px;*/
    /*}*/

    /* TOP FILTERS */

    .filters{
      display:flex;
      gap:14px;
      margin-bottom:26px;
    }

    .filters input{
      width:180px;
      height:34px;
      border:1px solid #dcdcdc;
      border-radius:5px;
      padding:0 14px;
      font-size:14px;
      outline:none;
    }

    .filters button {
    width: 140px;
    border: none;
    background: #ffffff;
    color: #878a95;
    border-radius: 5px;
    border: #69db3d solid 2px;
    font-weight: 600;
    cursor: pointer;
    font-size: 15px;
}
.filters button:hover{
     background: #6fd943;
    color: #fff;
}
.download-btn:hover {
    background: #6fd943;
    color: #ffffff;
}

    /* HERO */

    .hero{
      display:flex;
      gap:30px;
      align-items:flex-start;
    }

    /* TABLE */

    .table-wrapper{
      width:820px;
    }

    table{
      width:100%;
      border-collapse:collapse;
      border:1px solid #dddddd;
      overflow:hidden;
      border-radius:6px;
    }

    thead{
      background:#67d63b;
    }

    thead th{
      color:#fff;
      text-align:left;
      padding:14px 16px;
      font-size:14px;
      font-weight:600;
    }

    tbody td{
      padding:16px;
      border-bottom:1px solid #e8e8e8;
      font-size:13px;
      color:#333;
      vertical-align:top;
    }

    tbody tr:nth-child(even){
      background:#f6fbf4;
    }

    .small{
      display:block;
      font-size:11px;
      color:#777;
      margin-top:5px;
    }

    .view-btn{
      border:1px solid #cfcfcf;
      background:#fff;
      height:28px;
      padding:0 18px;
      border-radius:5px;
      cursor:pointer;
      font-size:12px;
      font-weight:500;
    }

    .table-footer{
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-top:1px;
    }

    .pagination{
      display:flex;
      align-items:center;
      gap:8px;
      font-size:13px;
    }

    .page{
      width:28px;
      height:28px;
      display:flex;
      align-items:center;
      justify-content:center;
      border:1px solid #ddd;
      cursor:pointer;
    }

    /*.active{*/
    /*  background:#69db3d;*/
    /*  color:#fff;*/
    /*  border:none;*/
    /*}*/

    .download-btn{
      background: #ffffff;
    color: #878a95;
    border: 2px solid #69db3d;
      height:38px;
      padding:0 18px;
      border-radius:5px;
      font-size:13px;
      font-weight:600;
      cursor:pointer;
    }

.slider-container {
  background: #e7fde7;
  padding: 20px;
  border-radius: 10px;
  position: relative;
}
.testimonial-slide {
    display: none;
    background: #fff;
    padding: 20px;
    margin-top: 20px;
    border-radius: 5px;
}
.slider-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.slider-heading {
  letter-spacing: 2px;
      color: var(--heading-color);
    font-family: var(--heading-font);
    font-weight: 600;
    font-size: 24px;
    line-height: 1.0833333333;
}

.nav-buttons {
  display: flex;
  gap: 10px;
}

.nav-buttons button {
  background: #ccc;
  border: none;
  padding: 8px 12px;
  cursor: pointer;
}


.testimonial-slide.active {
  display: block;
}

.logo {
  font-weight: bold;
  margin-bottom: 10px;
}

.read-more {
  margin-top: 20px;
  color: #333;
  cursor: pointer;
}

    /* SIDE CARD */
.side-card{
  width:445px;
  background:#e7fde7;
  border-radius:10px;
  padding:15px 34px;
  color:#145b08;
  border:1px solid #cceccc;
}

.side-heading,.side-card-heading{
  font-size:30px;
  line-height:1.2;
  margin-bottom:18px;
  font-weight:700;
  color:#145b08;
}

.side-card p{
  font-size:15px;
  line-height:1.7;
  color:#4b6b4b;
  margin-bottom:30px;
}

.side-card input{
  width:100%;
  height:45px;
  border:1px solid #b7ddb7;
  background:#ffffff;
  border-radius:5px;
  padding:0 18px;
  color:#145b08;
  margin-bottom:22px;
  outline:none;
}

.side-card input::placeholder{
  color:#7ca37c;
}

.side-card button{
  width:100%;
  height:45px;
      border: 2px solid #6fd943;
    background: #ffffff;
    color: #686868;

  border-radius:5px;
  font-weight:700;
  font-size:15px;
  cursor:pointer;
  transition:0.3s;
}

.side-card button:hover {
    background: #6fd943;
    color: #ffffff;
}
    /* STATS */

    .stats{
      display:flex;
      justify-content:space-between;
      margin:25px 0 45px;
      align-items:center;
    }
    .stats-cmp-main{
        display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    padding: 10px 0px;
    }
.stat-comp {
  display: flex;
    align-items: stretch;
    gap: 10px;
    flex-wrap: nowrap;
    align-content: space-around;
    flex-direction: row;
}
.stat-comp-icon
 {
    width: 15px;
    height: 30px;
    min-width: 30px;
    border-radius: 50%;
    background: #7be33f;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-size: 15px;
    font-weight: 700;
    line-height: 1;
}
    .stat{
      text-align:center;
    }

    .stat span{
      color:#69db3d;
      font-size:20px;
      margin-bottom:8px;
    font-weight: 700;
    }

    .stat p{
      font-size:14px;
      color:#444;
          font-weight: 700;
    }

    .logos {
  width: 55%;
  overflow: hidden;
  position: relative;
}

.logo-slider {
  display: flex;
  align-items: center;
  width: max-content;

  /* Start from half/right side */
  padding-left: 50%;

  animation: scrollLogo 35s linear infinite;
}

.logo-slider img {
 
  margin: 0 12px;
  object-fit: contain;
  white-space: nowrap;
}

/* Infinite smooth scroll */
@keyframes scrollLogo {
  from {
    transform: translateX(0);
  }

  to {
    transform: translateX(-50%);
  }
}

    /* DESCRIPTION */

    .description{
      font-size:15px;
      color:#444;
      line-height:1.9;
      margin-bottom:40px;
    }


    /* SECTION */

    .section-flex{
      display:flex;
      justify-content:space-between;
      gap:40px;
      margin-bottom:90px;
    }

    .left{
      width:68%;
    }

    .right{
      width:32%;
    }

    .title{
      font-size:35px;
      line-height:1.2;
      margin-bottom:36px;
      font-weight:700;
    }

    /* FEATURE TAGS */

    .tags{
      display:flex;
      flex-wrap:wrap;
      gap:18px;
    }

    .tag{
      background:#eef8ea;
      border-radius:40px;
      
    padding: 9px 14px;
      font-size:16px;
      font-weight:600;
      color:#3b3b3b;
      min-width:240px;
      display:flex;
    gap: 10px;
    flex-wrap: nowrap;
       align-content: space-between;
    justify-content: flex-start;
    align-items: center;
    }

    /* RELATED LIST */

    /* RELATED LIST */

.related-box{
  background:#eef8ea;
  border-radius:12px;
  padding:22px;
}

.related-header{
  display:flex;
  gap:10px;
  margin-bottom:22px;
  border-bottom:none;
}

.related-header .nav-item{
  flex:1;
}

.related-header .nav-link{
  width:100%;
  height:42px;
  border:none !important;
  border-radius:5px !important;
  font-weight:600;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#fff;
  color:#777;
  padding:0;
}

/* ACTIVE TAB */
.related-header .nav-link.active{
  background:#69db3d !important;
  color:#fff !important;
}

.related-box ul{
  list-style:none;
  padding-left:0;
  margin-bottom:0;
}

.related-box li{
  margin-bottom:16px;
  font-size:15px;
  color:#333;
}

    /* WHY CARDS */

    .why-grid{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:24px;
    }

    .why-card{
      background:#6fd943;
      color:#fff;
      border-radius:10px;
      padding:34px;
    }

    .why-card h3{
      font-size:24px;
      margin-bottom:10px;
          color: #ffffff;
    }

    .why-card p{
      color:#ffffff;
      line-height:1.6;
    }

    .testimonial{
      background:#eef8ea;
      border-radius:12px;
      padding:34px;
      line-height:1.9;
      color:#333;
      font-size:15px;
    }

    .testimonial strong{
      display:block;
      margin-top:30px;
      color:#111;
    }

    /* VIDEO SECTION */

    .video-box{
      background:#eef8ea;
      border-radius:12px;
      height:450px;
      display:flex;
      justify-content:center;
      align-items:center;
      font-size:90px;
      color:#145b08;
      position:relative;
    }

   /* TRUST */

.trust{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:90px;
}

.trust-item{
    text-align:center;
    opacity:.85;
}


.trust-item img{
    width:150px;
}

.trust-item h2{
    font-size:58px;
    color:#9d9d9d;
    font-weight:700;
}
.rating-box{
    display: flex;
    gap: 10px;
        padding: 10px;
}
.rating-text{
        font-size: 18px;
}

.stars{
    color:#FFD700;
        font-size: 18px;
    letter-spacing:3px;
}
/* SECTION TITLE */

.section{
    margin-bottom:90px;
}

.section-flex{
    display:flex;
    justify-content:space-between;
    gap:34px;
}

.left{
    width:66%;
}

.right{
    width:34%;
}

.section-title{
    font-size:48px;
    line-height:1.2;
    font-weight:700;
    margin-bottom:34px;
}

/* DECISION TABLE */

.decision-table{
    border-radius:10px;
    overflow:hidden;
}

.table-row{
    display:grid;
    grid-template-columns:1fr 1fr;
}

.cell-green{
    background:#e7fde7;
    color:#111;
    padding:18px 20px;
    font-size:15px;
    font-weight:500;
    border-bottom:1px solid rgba(255,255,255,.25);
}

.cell-light{
    background:#eaf6e5;
    color:#222;
    padding:18px 20px;
    font-size:15px;
    font-weight:500;
    border-bottom:1px solid #d7e7d1;
}

/* SMALL CARD */

.small-card{
    background:#e7fde7;
    border:2px solid #b7e0a8;
    border-radius:10px;
    padding:28px;
    text-align:center;
}

.logo{
    font-size:42px;
    font-weight:800;
    color:#145a0b;
    margin-bottom:28px;
}

.ase-grid{
    display:flex;
    justify-content:center;
    gap:22px;
    margin-bottom:26px;
}

.ase-item{
    text-align:center;
}

.ase-box{
    width:68px;
    height:68px;
    background:#67d63d;
    border-radius:10px;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#fff;
    font-size:42px;
    font-weight:700;
    margin-bottom:10px;
}

.ase-item p{
    font-size:14px;
    color:#333;
}

.small-card-button{
    width:100%;
    height:48px;
        background: #ffffff;
    border: 2px solid #6fd943;
    border-radius:6px;
    font-size:14px;
    font-weight:600;
    padding:12px;
    color:#000000;
    /*cursor:pointer;*/
}
.small-card button:hover {
    background: #6fd943;
    color: #ffffff;
}
/* GREEN ROI SECTION */

.roi-section{
    background:#145a0b;
    border-radius:12px;
    padding:50px 60px;
    text-align:center;
    color:#fff;
    margin-bottom:100px;
}

.roi-section span{
    font-size:24px;
    margin-bottom:8px;
    font-weight:600;
    color:#fff;
}

.roi-section h2{
    font-size:52px;
    margin-bottom:10px;
    font-weight:800;
     color:#fff;
}

.roi-section p{
    color:#d5f2cf;
    margin-bottom:44px;
     color:#fff;
}

.icon-grid{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:40px;
}

.icon-item{
    text-align:center;
    width:20%;
}

.icon{
    font-size:64px;
    margin-bottom:18px;
}

.icon-item p{
    font-size:20px;
    font-weight:600;
     color:#fff;
}

.roi-btn{
    width:320px;
    height:56px;
    background:#67d63d;
    border:none;
    border-radius:6px;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
}

/* BENEFITS */

.benefits{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:26px;
}

.benefit-card{
    background:#eaf6e5;
    border-radius:10px;
    padding:36px;
}

.benefit-card h3{
    font-size:28px;
    margin-bottom:28px;
    font-weight:700;
}

.benefit-card ul{
    list-style:none;
}

.benefit-card li{
    margin-bottom:18px;
    font-size:15px;
    color:#333;
    display:flex;
    align-items:center;
    gap:12px;
}

/* CTA SECTION */

.cta{
    margin-top:90px;
    background:#e7fde7;
    border-radius:12px;
    padding:60px;
    text-align:center;
}

.cta h2{
    font-size:54px;
    line-height:1.2;
    margin-bottom:18px;
    font-weight:800;
}

.cta p{
    color:#444;
    margin-bottom:34px;
    font-size:15px;
}

.cta-form{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:16px;
    margin-bottom:36px;
}

.cta-form input{
    width:360px;
    height:56px;
    border:none;
    border-radius:6px;
    padding:0 18px;
    font-size:14px;
    outline:none;
}

.orange-btn{
    width:180px;
    height:56px;
    border:none;
    border-radius:6px;
    background:#6fd943;
    color:#fff;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
}

.demo-btn {
    width: 160px;
    height: 56px;
    border: none;
    border-radius: 6px;
    background: #6fd943a1;
    color: #292929;
    font-size: 15px;
    padding:18px;
    font-weight: 700;
    cursor: pointer;
}

/* FEATURES */

.cta-features{
    display:flex;
    justify-content:center;
    gap:40px;
    flex-wrap:wrap;
}

.cta-feature{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:14px;
    color:#333;
}

.check{
    width:22px;
    height:22px;
    background:#6fd943;
    border-radius:50%;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:12px;
}

@media(max-width:1300px){

.container{
    width:95%;
}

.section-flex{
    flex-direction:column;
}

.left,
.right{
    width:100%;
}

.icon-grid{
    flex-wrap:wrap;
    gap:30px;
}

.icon-item{
    width:45%;
}

.benefits{
    grid-template-columns:1fr;
}

.cta-form{
    flex-direction:column;
}

.trust{
    flex-wrap:wrap;
    gap:40px;
}

}

/* TITLE */

.section-title{
    font-size:46px;
    font-weight:700;
    margin-bottom:34px;
}

/* INDUSTRY TABLE */

.industry-table{
    width:100%;
    border-collapse:collapse;
    margin-bottom:44px;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
}

.industry-table td{
    border:1px solid #e5e5e5;
    padding:18px 22px;
    font-size:14px;
    color:#333;
}

.highlight{
    background:#67d63d;
    color:#fff;
    font-weight:600;
}

/* GREEN BANNER */

/* GREEN BANNER */

.green-banner{
    width:100%;
    min-height:125px;
    background:#e7fde7;
    border-radius:14px;
    display:flex;
    align-items:center;
    gap:28px;
    padding:28px 34px;
    margin-bottom:70px;
    
    flex-direction: column;
}

/* ICON */

.banner-icon{
    width:66px;
    height:66px;
    min-width:66px;
    border-radius:50%;
    background:#7be33f;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#000;
    font-size:48px;
    font-weight:700;
    line-height:1;
}

/* TEXT */

.banner-text{
    color:#000;
    font-size:30px;
    font-weight:700;
    line-height:1.2;
    max-width:760px;
    text-align:center;
}

/* MOBILE */

@media(max-width:768px){

.green-banner{
    flex-direction:column;
    align-items:flex-start;
    padding:24px;
}

.banner-text{
    font-size:24px;
}

.banner-icon{
    width:56px;
    height:56px;
    min-width:56px;
    font-size:38px;
}

}

.check-circle{
    width:56px;
    height:56px;
    border-radius:50%;
    background:#67d63d;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#145a0b;
    font-size:32px;
    font-weight:900;
}


/* =====================================
INDUSTRY SECTION
===================================== */

.industry-section{
    margin:90px 0;
}

/* TITLE */

.industry-title{
    font-size:48px;
    font-weight:700;
    margin-bottom:40px;
    color:#111;
    line-height:1.2;
}

/* FLEX */

.industry-flex{
    display:flex;
    gap:30px;
    align-items:flex-start;
}

/* =====================================
LEFT TABS
===================================== */

.industry-tabs{
    width:300px;
    display:flex;
    flex-direction:column;
    gap:14px;
}

/* TAB BUTTON */

.industry-tab-btn{
    width:100%;
    min-height:58px;
    border:none;
    border-radius:8px;
    background:#f3f3f3;
    padding:9px 12px;
    text-align:left;
    font-size:15px;
    font-weight:600;
    color:#222;
    cursor:pointer;
    transition:.3s ease;
}

.industry-tab-btn:hover{
    background:#e7f6df;
}

.industry-tab-btn.active{
    background:#67d63d;
    color:#111;
    border: 2px solid #67d63d !important;
}

/* =====================================
RIGHT CONTENT
===================================== */

.industry-content{
    flex:1;
}

/* TAB CONTENT */

.industry-tab-content{
    display:none;
}

.industry-tab-content.active{
    display:block;
}

/* =====================================
LIST DESIGN
===================================== */

.industry-list{
    list-style:none;
    margin:0;
    padding:0;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    border-top:1px solid #dcdcdc;
    border-left:1px solid #dcdcdc;
    border-radius:8px;
    overflow:hidden;
}

/* LI */

.industry-list li{
    margin:0;
    padding:0;
}

/* SPAN */

.industry-list li a{
    display:flex;
    align-items:center;
    width:100%;
    min-height:71px;
    background:#f7f7f7;
    border-right:1px solid #dcdcdc;
    border-bottom:1px solid #dcdcdc;
       padding: 12px 10px;
    font-size: 14px;
    font-weight:500;
    color:#222;
    transition:.3s ease;
}

/* ALTERNATE BG */

.industry-list li:nth-child(6n+4) a,
.industry-list li:nth-child(6n+5) a,
.industry-list li:nth-child(6n+6) a{
    background:#eef4ec;
}

/* HOVER */

.industry-list li a:hover{
    background:#e4f7dc;
}

/* ACTIVE LEFT BORDER */

.industry-list li:first-child a{
    border-left:6px solid #67d63d;
    padding-left:20px;
}

/* OPTIONAL HIGHLIGHT */

.industry-list li a.highlight{
    border-left:6px solid #67d63d;
    padding-left:20px;
}

/* =====================================
RESPONSIVE
===================================== */

@media(max-width:1200px){

.industry-list{
    grid-template-columns:repeat(2,1fr);
}

}

@media(max-width:991px){
    .filters {
    display: flex;
    gap: 14px;
    margin-bottom: 26px;
    flex-direction: column;
    flex-wrap: wrap;
    align-content: center;
}
.filters button {
  width: 100%;
           border: 2px solid #69db3d;
        background: #ffffff;
        color:#878a95;
    border-radius: 5px;
    font-weight: 600;
    cursor: pointer;
    padding: 10px;
    font-size: 15px;
}

.filters input{
        height: 42px;
    border: 1px solid #dcdcdc;
    border-radius: 5px;
    padding: 0 14px;
    font-size: 14px;
    outline: none;
    margin-top: 20px;
    width: 100%;
}
.industry-flex{
    flex-direction:column;
}

.industry-tabs{
    width:100%;
}

.industry-list{

        grid-template-columns: repeat(2, 1fr);
}

.industry-title{
    font-size:36px;
}

.industry-list li a{
    min-height:auto;
    padding:18px;
    font-size:14px;
}

}

/* FAQ */

.faq-box{
    background:#f1f1f1;
    border-radius:12px;
    padding:42px;
    margin-bottom:90px;
}

.faq-heading{
    font-size:54px;
    font-weight:800;
    margin-bottom:8px;
}

.faq-sub{
    color:#666;
    font-size:14px;
    margin-bottom:40px;
}

.faq-item{
    border-top:1px solid #dddddd;
}

.faq-item:first-of-type{
    border-top:none;
}

.question{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:20px;
}

.question h3{
    font-size:28px;
    font-weight:600;
    line-height:1.5;
}

.icon{
    font-size:34px;
    color:#67d63d;
    font-weight:700;
}

.answer{
    margin-top:12px;
    color:#666;
    line-height:1.8;
    font-size:15px;
    max-width:1000px;
}

/* SAMPLE DATA SECTION */

.sample-section{
    background:#eaf6e5;
    border:2px solid #9be07e;
    border-radius:12px;
    padding:46px;
    display:flex;
    justify-content:space-between;
    gap:50px;
    margin-bottom:100px;
}

.sample-left{
    width:40%;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.sample-left h2{
    font-size:54px;
    line-height:1.2;
    margin-bottom:20px;
    font-weight:800;
}

.sample-left p{
    color:#555;
    font-size:15px;
    line-height:1.8;
}

.sample-right{
    width:60%;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
    margin-bottom:22px;
}

.form-grid input,
.form-grid select,
.form-grid textarea{
    width:100%;
    background:#fff;
    border:none;
    border-radius:6px;
    padding:18px;
    font-size:14px;
    outline:none;
    font-family:'Inter',sans-serif;
}

.form-grid textarea{
    height:100px;
    resize:none;
}

.full-btn{
    width:100%;
    height:56px;
    border:none;
    border-radius:6px;
    background:#67d63d;
    color:#111;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
}

/* BLOG SECTION */

.blog-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.blog-title h2{
    font-size:34px;
    font-weight:700;
}

.blog-title p{
    color:#666;
    font-size:14px;
    margin-top:6px;
}

.arrow-buttons{
    display:flex;
    gap:12px;
}

.arrow{
    width:48px;
    height:48px;
    border-radius:6px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:24px;
    cursor:pointer;
}

.arrow.gray{
    background:#ececec;
    color:#999;
}

.arrow.green{
    background:#67d63d;
    color:#111;
}


.related-blogs{
    background:#f5f5f5;
}

.blog-title{
    font-size:34px;
    font-weight:700;
    color:#07112b;
    margin-bottom:10px;
}


.read-btn{
background-color: var(--main-color-one);
    color: #fff;
    display: inline-block;
    padding: 12px 40px;
    border-radius: 25px;
    min-width: 160px;
    text-align: center;
    -webkit-transition: all .3s ease-in;
    -o-transition: all .3s ease-in;
    transition: all .3s ease-in;
    font-weight: 600;
    border: none;
}
.carousel-btn{
    width:34px;
    height:34px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#e7e7e7;
    font-size:28px;
    color:#555;
    text-decoration:none;
    transition:0.3s;
}

.carousel-btn.active{
    background:#69d33c;
    color:#fff;
}

.carousel-btn:hover{
    text-decoration:none;
    color:#fff;
    background:#69d33c;
}

.blog-card{
    margin-bottom:20px;
    padding: 10px;
    border: 1px solid #6fd943;
}

.blog-img{
    position:relative;
    overflow:hidden;
    border-radius:16px;
}

.blog-img img{
    width:100%;
    height:100%;
    object-fit:cover;
    border-radius:16px;
}

.blog-date{
    position:absolute;
    top:20px;
    left:20px;
    background:rgba(0,0,0,0.5);
    color:#fff;
    padding:6px 9px;
    border-radius:30px;
    font-size:16px;
}

.blog-content h3{
    font-size:28px;
    font-weight:700;
    line-height:1.4;
    color:#07112b;
    margin-top:25px;
}

@media(max-width:991px){

    .blog-title{
        font-size:40px;
    }

    .blog-content h3{
        font-size:22px;
    }

}
/* DESKTOP = 3 ITEMS */
@media (min-width: 992px) {

    .carousel-item .col-lg-4{
        display:block;
    }

}

/* TABLET = 2 ITEMS */
@media (min-width: 768px) and (max-width: 991px){

    .carousel-item .col-lg-4:nth-child(3){
        display:none;
    }

}

/* MOBILE = 1 ITEM */
@media (max-width: 767px){

    .carousel-item .col-md-6,
    .carousel-item .col-lg-4{
        display:none;
    }

    .carousel-item .col-md-6:first-child,
    .carousel-item .col-lg-4:first-child{
        display:block;
        max-width:100%;
        flex:0 0 100%;
    }

}
/* RESPONSIVE */

@media(max-width:1300px){

.container{
    width:95%;
}

.sample-section{
    flex-direction:column;
}

.sample-left,
.sample-right{
    width:100%;
}

.blog-grid{
    grid-template-columns:1fr;
}

.form-grid{
    grid-template-columns:1fr;
}

.question h3{
    font-size:22px;
}

.section-title,
.blog-title h2,
.sample-left h2{
    font-size:36px;
}


}
/* Tab SECTION */

/* FAQ SECTION */

.faq-section{
    margin:100px 0;
}

/* HEADING */

.faq-title{
    font-size:52px;
    font-weight:800;
    margin-bottom:14px;
    color:#111;
}

.faq-subtitle{
    font-size:15px;
    color:#666;
    margin-bottom:45px;
}

/* FAQ BOX */

.faq-wrapper{
    background:#e7fde7;
    border-radius:12px;
    overflow:hidden;
    border:1px solid #6fd943;
}

/* FAQ ITEM */

.faq-item{
    border-bottom:1px solid #dddddd;
}

.faq-item:last-child{
    border-bottom:none;
}

/* QUESTION */

.faq-question{
    width:100%;
    background:none;
    border:none;
   
    padding: 0px 15px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    cursor:pointer;
    text-align:left;
}

/* QUESTION TEXT */

.faq-question h3{
    font-size:20px;
    font-weight:500;
    color:#000;
    line-height:1.5;
    margin:0;
}

/* ICON */

.faq-icon{
    font-size:36px;
    color:#67d63d;
    font-weight:500;
    transition:.3s;
}

/* ANSWER */

.faq-answer{
    max-height:0;
    overflow:hidden;
    transition:max-height .4s ease;
    background:#fff;
}

.faq-answer-content{
  padding: 10px 15px 15px;
    font-size:15px;
    line-height:1.9;
    color:#000;
}

/* ACTIVE */

.faq-item.active .faq-answer{
    max-height:400px;
}

.faq-item.active .faq-icon{
    transform:rotate(45deg);
}

/* MOBILE */

@media(max-width:768px){

.faq-title{
    font-size:36px;
}

.faq-question{
    padding:22px;
}

.faq-question h3{
    font-size:18px;
}

.faq-answer-content{
    padding:0 22px 24px;
    font-size:14px;
}

}

/* SECTION */

.sample-data-section{
    margin:90px 0;
}

/* BOX */

.sample-box{
    background:#dff0d7;
    border:2px solid #2aa4ff;
    border-radius:8px;
    padding:38px 34px;
}

/* FLEX */

.sample-flex{
    display:flex;
    justify-content:space-between;
    gap:40px;
    align-items:center;
}

/* LEFT */

.sample-left{
    width:38%;
}

.sample-left h2{
    font-size:48px;
    line-height:1.1;
    font-weight:800;
    color:#111;
    margin-bottom:18px;
}

.sample-left p{
    font-size:15px;
    line-height:1.7;
    color:#333;
    max-width:400px;
}

/* RIGHT */

.sample-right{
    width:62%;
}

/* FORM GRID */

.sample-form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
    margin-bottom:22px;
}

/* INPUT */
.sample-form-grid textarea{
   
    
    resize:none;
    grid-row:span 2;
     width:100%;
    height:122px;
    border:none;
    border-radius:4px;
    background:#f5f5f5;
    padding:14px 16px;
    font-size:14px;
    color:#333;
    outline:none;
    font-family:Arial,sans-serif;
}

.sample-form-grid input,
.sample-form-grid select{
    width:100%;
    height:54px;
    border:none;
    border-radius:4px;
    background:#f5f5f5;
    padding:0 16px;
    font-size:14px;
    color:#333;
    outline:none;
    font-family:Arial,sans-serif;
}


/* PLACEHOLDER */

.sample-form-grid input::placeholder,
.sample-form-grid textarea::placeholder{
    color:#999;
}

/* BUTTON */

.sample-btn{
    width:100%;
    height:54px;
    border:none;
    border-radius:4px;
    background:#67d63d;
    color:#111;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
    transition:.3s;
}

.sample-btn:hover{
    opacity:.9;
}

/* MOBILE */

@media(max-width:991px){

.sample-flex{
    flex-direction:column;
}

.sample-left,
.sample-right{
    width:100%;
}

.sample-left h2{
    font-size:36px;
}

.sample-form-grid{
    grid-template-columns:1fr;
}

}
.table-wrapper {
  width: 100%;
  overflow: hidden;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.table {
  width: 100%;
  min-width: 775px;
  border-collapse: collapse;
}
   .hero-section {
      background: #eef8ea;
      padding: 0px 160px;
      position: relative;
    }

    .breadcrumb {
      font-size: 14px;
      color: #666;
      background-color: #eef8ea;
      margin-bottom: 1px;
    }

    .hero-title {
      text-align: center;
      font-size: 46px;
      font-weight: 700;
    padding: 0px 0px 6px;

      color: #000;
      line-height: 1.2;
    }

/* Tablet */
@media (max-width: 992px) {
  .hero-section {
    padding: 28px 20px;
  }
  .table-footer{
          gap: 10px;
    display: flex;
    align-items: center;
    margin-top: 18px;
    /*justify-content: flex-start;*/
    /*flex-direction: row;*/
  }
}

/* Mobile */
@media (max-width: 576px) {
  .hero-section {
    padding: 28px 20px;
  }
  .pagination-wrapper ul {
    display: flex;
    margin-top: 18px;
}
.side-card {
    width: 380px;
    background: #145b08;
    border-radius: 10px;
    padding: 42px 34px;
    color: #fff;
}
}
@media (max-width: 768px) {
  .table {
    min-width: 900px;
  }
  .hero {
    display: flex;
    gap: 30px;
    align-items: flex-start;
    flex-direction: column;
}
}
.table td, .table th{
        padding: .25rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
  </style>
  @php
    use Illuminate\Support\Str;
@endphp
    <section class="hero-section">
    <div class="breadcrumb">
      home / b2b /{{ \Illuminate\Support\Str::slug($service_item->title) }}
    </div>

    <h1 class="hero-title">
     {{$service_item->title}} 
    </h1>

  <!-- FILTERS -->

  <div class="filters">
      
                <form  enctype="multipart/form-data">
                    @csrf
    <input type="text" placeholder="Title"  id="search-title" value="{{ $service_item->search_title ?? '' }}">
    <input type="text" placeholder="Industry" id="search-industry"  value="{{ $service_item->search_industry ?? '' }}">
    <input type="text" placeholder="Business Category" id="search-business" value="{{$service_item->search_business_category ?? '' }}">
    <input type="text" placeholder="Location" id="search-location" value="{{$service_item->search_location ?? '' }}">
    <button type="button" onclick="fetchLeads()">Search</button>
    
     </form>
  </div>

  <!-- HERO -->

  <div class="hero">

    <!-- TABLE -->

    <div class="table-wrapper">
 <div class="table-responsive">

      <table class="table" id="leadsTable">

        <thead>
          <tr>
            <th>Title</th>
            <th>Company</th>
            <th>Person Name</th>
            <th>Email Id</th>
            <th>Industry</th>
          </tr>
        </thead>

        <tbody>


        </tbody>

      </table>
</div>
      <div class="table-footer">
<nav class="pagination-wrapper" aria-label="Page navigation "> 
                        <ul class="pagination justify-content-center" role="navigation">
        
                    <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                <span class="page-link" aria-hidden="true">«</span>
            </li>
        
        
                    
            
            
                                                                        <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                                                                                <li class="page-item">
                            <a href="https://app.go4database.com/login" target="_blank"  class="page-link" data-page="2">2</a>
                        </li>
                                                                                <li class="page-item">
                            <a href="https://app.go4database.com/login"  target="_blank" class="page-link" data-page="3">3</a>
                        </li>
                                                                                <li class="page-item">
                            <a href="https://app.go4database.com/login" target="_blank" class="page-link" data-page="4">4</a>
                        </li>
                                         
        
                    <li class="page-item">
                <a href="https://app.go4database.com/login"  target="_blank" class="page-link" data-page="2" aria-label="Next">
                    »
                </a>
            </li>
            </ul>
 </nav>
        
        <button class="download-btn">
         <!-- DOWNLOAD ICON -->
<a href="https://app.go4database.com/login" target="_blank"  >
<i class="fa fa-download"></i>  Download List</a>
        </button>

      </div>

    </div>

    <!-- SIDE CARD -->

    <div class="side-card">

      <div class="side-heading">Do you want more traffic?</div>

      <p>
        Hey, I’m Neil Patel. I’m determined to make a business grow.
        My only question is, will it be yours?
      </p>
<form action="https://app.go4database.com/register" method="get">
      <input type="text" name="website_url" placeholder="Your website URL">

      <button>Continue</button>
</form>
    </div>

  </div>

  </section>
  <div class="container">


  <!-- STATS -->

  <div class="stats">

    <div class="stat">
      <span>{{$service_item->data_counts}}</span>
      <p>Data Counts</p>
    </div>

    <div class="stat">
      <span>1-Apr-2026</span>
      <p>Last updated</p>
    </div>

    <div class="stat">
      <span>{{$service_item->accuracy_commitment}}</span>
      <p>Accuracy Commitment</p>
    </div>

    <div class="logos">

  <div class="logo-slider">

    <!-- Original Logos -->
    <img src="{{asset('assets/uploads/product/Candela.png')}}" width="92">
    <img src="{{asset('assets/uploads/product/Tecan.png')}}" width="89">
    <img src="{{asset('assets/uploads/product/Westin.png')}}"  width="66">
    <img src="{{asset('assets/uploads/product/CBRE.png')}}"width="66">
    <img src="{{asset('assets/uploads/product/Shopify.png')}}" width="46">
    <img src="{{asset('assets/uploads/product/MIQ.png')}}"   width="68">
    <img src="{{asset('assets/uploads/product/J Pocker.png')}}"  width="68">

    <!-- Duplicate Logos -->
    <img src="{{asset('assets/uploads/product/Candela.png')}}"  width="92">
    <img src="{{asset('assets/uploads/product/Tecan.png')}}" width="89">
    <img src="{{asset('assets/uploads/product/Westin.png')}}" width="66">
    <img src="{{asset('assets/uploads/product/CBRE.png')}}" width="66">
    <img src="{{asset('assets/uploads/product/Shopify.png')}}" width="46">
    <img src="{{asset('assets/uploads/product/MIQ.png')}}"  width="68">
    <img src="{{asset('assets/uploads/product/J Pocker.png')}}"  width="68">

  </div>
  <div class="stats-cmp-main">
<div class="stat-comp">
      <div class="stat-comp-icon">
        ✓
    </div>
      <p>GDPR Compliance</p>
    </div>

    <div class="stat-comp">
     <div class="stat-comp-icon">
        ✓
    </div>
      <p>CCPA Compliance</p>
    </div>

    <div class="stat-comp">
     <div class="stat-comp-icon">
        ✓
    </div>
      <p>ISO certified</p>
    </div>

</div>
</div>
  </div>

  <!-- DESCRIPTION -->

  <p class="description"> {{ strip_tags($service_item->description) }}
  </p>

  <!-- GREEN BANNER -->


  <!-- SECTION -->

  <div class="section-flex">
      

    <div class="left">

<div class="green-banner">

    <div class="banner-icon">
        ✓
    </div>

    <div class="banner-text">
        Get Instant Access to {{$service_item->data_counts}} Verified {{$service_item->title}}
    </div>
<a href="https://app.go4database.com/login?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=app_login" class="read-btn" target="_blank">
   Have you requested for free sample data yet?</a>
</div>
      <h2 class="title">
        Get Instant Access to 1200+ {{$service_item->title}}
      </h2>

      <div class="tags">

        <div class="tag"><img src="{{asset('assets/uploads/product/Verified Contacts.png')}}" width="46">Verified Contacts</div>
        <div class="tag"><img src="{{asset('assets/uploads/product/Decision Makers.png')}}"  width="46">Decision Makers</div>
        <div class="tag"><img src="{{asset('assets/uploads/product/GEO Targeting.png')}}"  width="46">GEO Targeting</div>
        <div class="tag"><img src="{{asset('assets/uploads/product/High ROI Driven.png')}}"  width="46">High ROI Driven</div>

      </div>

    </div>

    <!-- RELATED -->

<div class="right">

  <div class="related-box">

    <!-- Tabs -->
    <ul class="nav related-header" id="relatedTab" role="tablist">

      <li class="nav-item">
        <a class="nav-link active"
           id="list1-tab"
           data-toggle="tab"
           href="#list1"
           role="tab">
          Related List
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link"
           id="list2-tab"
           data-toggle="tab"
           href="#list2"
           role="tab">
          Industry List
        </a>
      </li>

    </ul>

    <!-- Tab Content -->
    <div class="tab-content">

      <!-- TAB 1 -->
      <div class="tab-pane fade show active"
           id="list1"
           role="tabpanel">

        <ul>
        @foreach($relatedServices as $item)
        <li>
        <a href="{{ route('frontend.services.single', [
        'service_slug' => ltrim($item->slug, '/')
        ]) }}">
        {{ $item->title }}
        </a>
        </li>
        @endforeach
        
        </ul>

      </div>

      <!-- TAB 2 -->
      <div class="tab-pane fade"
           id="list2"
           role="tabpanel">

        <ul>
           @foreach($all_serviceCat as $item)
           
               @php
        $exists = \App\Services::where('slug', ltrim($item->slug, '/'))->exists();
    @endphp
        <li>
            <a href="{{ $exists 
            ? route('frontend.services.single', ['service_slug' => ltrim($item->slug, '/')]) 
            : 'javascript:void(0)' }}">
            {{ $item->name }} Email List
        </a>
        </li>
    @endforeach
        </ul>

      </div>

    </div>

  </div>

</div>


  </div>

  <!-- WHY SECTION -->

  <div class="section-flex">

    <div class="left">

      <h2 class="title">
        Why Our {{$service_item->title}} List Delivers Better RO
      </h2>

      <div class="why-grid">

        <div class="why-card">
          <h3>Precision Targeting</h3>
          <p>Focus on high-intent dental decision makers</p>
        </div>

        <div class="why-card">
          <h3>Real-Time Data Updates</h3>
          <p>Fresh contacts with better deliverability</p>
        </div>

        <div class="why-card">
          <h3>AI-Powered Matching</h3>
          <p>Identify best-fit prospects faster</p>
        </div>

        <div class="why-card">
          <h3>Dedicated Expert Support</h3>
          <p>Custom list building assistance</p>
        </div>

      </div>

    </div>

    <div class="right">
<div class="slider-container">
  <div class="slider-header">
    <div class="slider-heading">Our Testimonial</div>
    <div class="nav-buttons">
      <button onclick="prevSlide()">&#10094;</button>
      <button onclick="nextSlide()">&#10095;</button>
    </div>
    
  </div>
   @foreach($all_testimonial as $testimonial)
  <div class="testimonial-slide  {{ $loop->first ? 'active' : '' }}">
    <div class="testimonial">

    {{ \Illuminate\Support\Str::limit(strip_tags($testimonial->description), 180) }}

        <strong>
        {{ $testimonial->name }}
        </strong>

        Customer

      </div>
  </div>
  @endforeach
  

  <div class="read-more"><a href="{{url('/our-testimonial')}}">Read all Testimonial →</a></div>
</div>
      

    </div>

  </div>

  <!-- VIDEO -->

  <div class="section-flex"  >

    <div class="left">

      <h2 class="title">
        Simple steps to get free {{$service_item->title}}
      </h2>

      <div class="video-box" style="background-image: url({{asset('assets/uploads/product/list.png')}});">
        ▶
      </div>

    </div>

    <div class="right">

      <div class="side-card">
           <div class="side-card-heading">Talk to marketing expert to get precise targeted mailing list</div>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
  <form action="{{ route('service-lead.submit') }}" method="POST">
        @csrf

        <input type="text" name="name" placeholder="Name" required>

        <input type="text" name="phone" placeholder="Phone" required>

        <input type="email" name="email" placeholder="Email" required>

        <input type="text" name="business_details" placeholder="Website/Business Details">

        <button type="submit">Continue</button>
    </form>

      </div>

    </div>

  </div>

  <!-- TRUST -->

  <div class="trust">

    <div class="trust-item">
      <img src="{{asset('assets/uploads/product/Trustpilot.webp')}}">
      <div class="rating-box">
      <div class="rating-text"> 4.5 </div><div class="stars">★★★★★</div>
    </div>
    </div>
    <div class="trust-item">
     <img src="{{asset('assets/uploads/product/G2.webp')}}"   style="width:40px!important;">
    <div class="rating-box">
      <div class="rating-text"> 4.5 </div><div class="stars">★★★★★</div>
    </div>
   </div>

    <div class="trust-item">
      <img src="{{asset('assets/uploads/product/datarade.webp')}}">
 <div class="rating-box">
      <div class="rating-text"> 4.5 </div><div class="stars">★★★★★</div>
    </div>
</div>

    <div class="trust-item">
      <img src="{{asset('assets/uploads/product/BBB.webp')}}">
   <div class="rating-box">
      <div class="rating-text"> 4.5 </div><div class="stars">★★★★★</div>
    </div>
  </div>

  </div>


<!-- DECISION MAKERS -->

<div class="section">

<div class="section-flex">

<div class="left">

<h2 class="section-title">
Key Decision Makers in the {{$service_item->title}}
</h2>

<div class="decision-table">
<div class="vertical-table">

    <div class="table-row">
        <div class="table-label">Chief Executive Officer (CEO)</div>
        <div class="table-value">Chief Operating Officer (COO)</div>
    </div>

    <div class="table-row">
        <div class="table-label">Chief Marketing Officer (CMO)</div>
        <div class="table-value">Chief Financial Officer (COO)</div>
    </div>

    <div class="table-row">
        <div class="table-label">Vice President</div>
        <div class="table-value">Director</div>
    </div>

    <div class="table-row">
        <div class="table-label">Business Development Manager</div>
        <div class="table-value">Procurement Manager</div>
    </div>

</div>

<style>
.vertical-table{
    width:100%;
    border:1px solid #ddd;
    border-radius:8px;
    overflow:hidden;
    font-family:Arial, sans-serif;
}

.table-row{
    display:flex;
    border-bottom:1px solid #ddd;
}

.table-row:last-child{
    border-bottom:none;
}

.table-label{
    width: 40%;
    background: #67d63deb;
    padding: 14px;
    color: #000;
    font-weight: 500;
    text-align: center;
    border-right: 1px solid #67d63b;
}

.table-value{
    flex:1;
    padding:14px;
    background: #e7fde7;
    color: #000;
}
</style>



</div>

</div>

<!-- SMALL CARD -->

<div class="right">

<div class="small-card">

<div class="logo">
<img src="https://www.go4database.com/assets/uploads/media-uploader/go4database-logo17535121121771578926.webp">
</div>

<div class="ase-grid">

<div class="ase-item">
<div class="ase-box">a</div>
<p>Active</p>
</div>

<div class="ase-item">
<div class="ase-box">s</div>
<p>Specific</p>
</div>

<div class="ase-item">
<div class="ase-box">e</div>
<p>Effortless</p>
</div>

</div>

<div class="small-card-button">
Use it then Believe it
</div>

</div>

</div>

</div>

</div>

<!-- ROI SECTION -->

<div class="roi-section">

<span>
Grow your ROI with Result Focused
</span>

<h2>
Global B2B Contact Data
</h2>

<p>
Choose data as per your specific criteria
</p>

<div class="icon-grid">

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Industry - Green.png')}}"></div>
<p>Industry</p>
</div>

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Job Titile - Green.png')}}"></div>
<p>Job Title</p>
</div>

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Location - White.png')}}"></div>
<p>Location</p>
</div>

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Employee Size - Green.png')}}"></div>
<p>Employee Size</p>
</div>

<div class="icon-item">
<div class="icon"><img src="{{asset('assets/uploads/product/Reveue Size - Green.png')}}"></div>
<p>Revenue Size</p>
</div>

</div>

<a href="https://app.go4database.com/login?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=app_login" class="read-btn" target="_blank" class="roi-btn">
Have you requested for free sample data yet?
</a>

</div>

<!-- BENEFITS -->

<div class="section">

<h2 class="section-title">
Who Can Benefit from Our {{$service_item->title}} to Boost ROI?
</h2>

<div class="benefits">
@php
    $buyers = array_filter(
        array_map('trim', explode(',', $service_item->buyerlist ?? ''))
    );

    $chunks = array_chunk(
        $buyers,
        max(1, ceil(count($buyers) / 2))
    );
@endphp

@if(count($buyers))
    @foreach($chunks as $chunk)
        <div class="benefit-card">
            <ul>
                @foreach($chunk as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach
@else
    <div class="benefit-card">
        <p>No entries available.</p>
    </div>
@endif

</div>

</div>

<!-- CTA -->

<div class="cta">

<h2>
Try Book Your Data with 100 Free Credits, no Credit Card Required.
</h2>

<p>
No subscription commitments or mandatory demos.
Discover the power of 97% accurate, laser targeted data on your terms.
</p>

<div class="cta-form">
<form action="https://app.go4database.com/register" method="get">
<input name="email" type="text" placeholder="Enter your work email">

<button class="orange-btn">
Get 100 Free Leads
</button>
</form>
<span>OR</span>

<a href="https://app.go4database.com/login?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=app_login" class="demo-btn">
Book a Demo
</a>

</div>

<div class="cta-features">

<div class="cta-feature">
<div class="check">✔</div>
Real time email verification
</div>

<div class="cta-feature">
<div class="check">✔</div>
95% data accuracy guarantee
</div>

<div class="cta-feature">
<div class="check">✔</div>
GDPR & CCPA compliant
</div>

</div>

</div>


<!-- SECTION -->


<div class="industry-section">

    <h2 class="industry-title">
        Other Targeted Industry Mailing List
    </h2>

    <div class="industry-flex">

        <!-- LEFT TAB -->

        <div class="industry-tabs">
            
            @foreach($all_serviceCat as $key => $category)

            <button class="industry-tab-btn {{ $key == 0 ? 'active' : '' }}"
                    data-tab="industry{{ $category->id }}">
               {{ $category->name }}
            </button>
@endforeach
            

        </div>

        <!-- RIGHT CONTENT -->

        <div class="industry-content">

            <!-- TAB 1 -->
            <div class="industry-tab-content active"
                 id="industry1">

          <ul class="industry-list">
@php
$titles = [
    'Hospitals Email List',
    'Chiropractic Clinics Database',
    'Pharma CEO Email List',
    'Multi-Specialty Clinics Mailing List',
    'Acupuncture Clinics Email List',
    'Medical Equipment CEO Contact List',
    'Pharmaceutical Companies Database',
    'Holistic Wellness Centers Mailing List',
    'Medical Device VP Mailing List',
    'Medical Device Manufacturers Contact List',
    'IV Therapy Clinics Email List',
    'Healthcare SaaS Founder Database',
    'Healthcare SaaS Providers Email List',
    'Nutrition Consultants Contact List',
    'Telehealth CEO Contact List',
    'Telemedicine Platforms Database',
    'Hearing Aid Clinics Database',
    'Clinical Research VP Database',
    'Health Insurance Providers Mailing List',
    'Speech Therapy Centers Mailing List',
    'Medical Billing Owner Mailing List',
    'Clinical Research Organizations Database',
    'Occupational Therapy Clinics Email List',
    'Healthcare Compliance Manager List',
    'Diagnostic Imaging Centers Contact List',
    'Alternative Medicine Clinics Database',
    'Rehab Center CEO Database',
    'Medical Billing Companies Email List',
    'Rural Medical Practices Mailing List',
    'Healthcare Recruiter Contact List',
    'Home Healthcare Agencies Database',
    'Elderly Care Consultants Contact List',
    'Wellness Brand Founder Email List',
    'Laboratory Networks Mailing List',
    'Independent Therapists Email List',
    'Healthcare CRM Executive Database',
    'Rehabilitation Centers Contact List',
    'Wellness Retreat Centers Database',
    'Lab Network CEO Mailing List',
    'Healthcare Staffing Agencies Email List',
    'Medical Tourism Agencies Mailing List',
    'Healthcare Analytics VP Contact List',
    'Healthcare CRM Providers Database',
    'Mobile Healthcare Units Contact List',
    'Assisted Living Owner Database',
];
@endphp
           @foreach($titles as $title)

    @php
        $slug = \Illuminate\Support\Str::slug($title);

        $service = \App\Services::where('slug', 'LIKE', '%'.$slug)->first();

        $url = $service
            ? route('frontend.services.single', [
                'service_slug' => ltrim($service->slug, '/')
            ])
            : 'javascript:void(0);';
    @endphp

    <li>
        <a href="{{ $url }}">
            {{ $title }}
        </a>
    </li>

@endforeach
 
  </ul>

            </div>
            <!-- TAB 2 -->
        <div class="industry-tab-content" id="industry2">

                <ul class="industry-list">
                    <li> <a href="javascript:void(0);">Real Estate Investors Email List</a></li>
                    <li> <a href="javascript:void(0);">Multifamily Property Buyers Database	</a></li>
                    <li> <a href="javascript:void(0);">Real Estate CEO Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Commercial Real Estate Firms Contact List</a></li>
                    <li> <a href="javascript:void(0);">Office Leasing Companies Email List</a></li>
                    <li> <a href="javascript:void(0);">Commercial Property VP Database</a></li>
                    <li> <a href="javascript:void(0);">Property Management Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">HOA Management Services Contact List</a></li>
                    <li> <a href="javascript:void(0);">Property Operations Manager Email List</a></li>
                    <li> <a href="javascript:void(0);">Real Estate Developers Database</a></li>
                    <li> <a href="javascript:void(0);">Land Development Firms Mailing List	Real Estate </a></li>
                    <li> <a href="javascript:void(0);">Founder Contact List</a></li>
                    <li> <a href="javascript:void(0);">Residential Brokerage Firms Email List</a></li>
                    <li> <a href="javascript:void(0);">Luxury Home Realtors Database</a></li>
                    <li> <a href="javascript:void(0);">Brokerage Owner Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Mortgage Lenders Contact List</a></li>
                    <li> <a href="javascript:void(0);">Hard Money Lending Companies Email List</a></li>
                    <li> <a href="javascript:void(0);">Mortgage CEO Database</a></li>
                    <li> <a href="javascript:void(0);">Real Estate SaaS Providers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Property CRM Platforms Contact List</a></li>
                    <li> <a href="javascript:void(0);">PropTech Founder Email List</a></li>
                    <li> <a href="javascript:void(0);">Title Companies Database</a></li>
                    <li> <a href="javascript:void(0);">Escrow Service Providers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Title Agency Owner Contact List</a></li>
                    <li> <a href="javascript:void(0);">Real Estate Investment Trusts Email List</a></li>
                    <li> <a href="javascript:void(0);">Industrial REIT Firms Database</a></li>
                    <li> <a href="javascript:void(0);">Investment VP Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Vacation Rental Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Airbnb Property Managers Email List</a></li>
                    <li> <a href="javascript:void(0);">Short-Term Rental CEO Database</a></li>
                    <li> <a href="javascript:void(0);">Construction Management Firms Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Commercial Renovation Contractors Contact List</a></li>
                    <li> <a href="javascript:void(0);">Construction Project Owner Email List</a></li>
                    <li> <a href="javascript:void(0);">Senior Housing Providers Database</a></li>
                    <li> <a href="javascript:void(0);">Assisted Living Communities Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Senior Living CEO Contact List</a></li>
                    <li> <a href="javascript:void(0);">Student Housing Companies Email List</a></li>
                    <li> <a href="javascript:void(0);">University Apartment Operators Database</a></li>
                    <li> <a href="javascript:void(0);">Housing Operations Manager Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Real Estate Staffing Agencies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Leasing Recruitment Firms Email List</a></li>
                    <li> <a href="javascript:void(0);">Real Estate Recruiter Database</a></li>
                    <li> <a href="javascript:void(0);">Self Storage Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Mini Storage Facility Operators Contact List</a></li>
                    <li> <a href="javascript:void(0);">Storage Facility Owner Email List</a></li>
                    
                    
                </ul>

            </div>

      <!-- TAB 3 -->
        <div class="industry-tab-content" id="industry3">

                <ul class="industry-list">
                    <li> <a href="javascript:void(0);">Technology Industry Email List</a></li>
                    <li> <a href="javascript:void(0);">Cloud Computing Companies Database</a></li>
                    <li> <a href="javascript:void(0);">Technology CEO Email List</a></li>
                    <li> <a href="javascript:void(0);">Software Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">SaaS Providers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Software Founder Database</a></li>
                    <li> <a href="javascript:void(0);">IT Services Database</a></li>
                    <li> <a href="javascript:void(0);">Managed IT Services Email List</a></li>
                    <li> <a href="javascript:void(0);">IT Director Contact List</a></li>
                    <li> <a href="javascript:void(0);">Cybersecurity Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Network Security Vendors Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Cybersecurity CEO Database</a></li>
                    <li> <a href="javascript:void(0);">Artificial Intelligence Email List</a></li>
                    <li> <a href="javascript:void(0);">Machine Learning Startups Database</a></li>
                    <li> <a href="javascript:void(0);">AI Founder Contact List</a></li>
                    <li> <a href="javascript:void(0);">Data Center Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Colocation Providers Email List</a></li>
                    <li> <a href="javascript:void(0);">Data Center VP Database</a></li>
                    <li> <a href="javascript:void(0);">Fintech Technology Database</a></li>
                    <li> <a href="javascript:void(0);">Digital Payment Platforms Contact List</a></li>
                    <li> <a href="javascript:void(0);">Fintech CEO Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Telecommunications Companies Email List</a></li>
                    <li> <a href="javascript:void(0);">VoIP Service Providers Database</a></li>
                    <li> <a href="javascript:void(0);">Telecom Manager Contact List</a></li>
                    <li> <a href="javascript:void(0);">Healthcare Technology Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Medical Software Vendors Contact List</a></li>
                    <li> <a href="javascript:void(0);">HealthTech Founder Email List</a></li>
                    <li> <a href="javascript:void(0);">EdTech Companies Database</a></li>
                    <li> <a href="javascript:void(0);">Online Learning Platforms Mailing List</a></li>
                    <li> <a href="javascript:void(0);">EdTech CEO Contact List</a></li>
                    <li> <a href="javascript:void(0);">Ecommerce Technology Email List</a></li>
                    <li> <a href="javascript:void(0);">Retail Software Providers Database</a></li>
                    <li> <a href="javascript:void(0);">Ecommerce VP Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Blockchain Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Crypto Technology Firms Database</a></li>
                    <li> <a href="javascript:void(0);">Blockchain Founder Mailing List</a></li>
                    <li> <a href="javascript:void(0);">HR Technology Database</a></li>
                    <li> <a href="javascript:void(0);">Recruitment Software Companies Email List</a></li>
                    <li> <a href="javascript:void(0);">HR Tech Recruiter Contact List</a></li>
                    <li> <a href="javascript:void(0);">Marketing Technology Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Automation Software Vendors Database</a></li>
                    <li> <a href="javascript:void(0);">MarTech Manager Email List</a></li>
                    <li> <a href="javascript:void(0);">Enterprise Software Contact List</a></li>
                    <li> <a href="javascript:void(0);">CRM Software Providers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Enterprise CIO Database</a></li>
                    
                    
                </ul>

            </div>

      <!-- TAB 4 -->
        <div class="industry-tab-content" id="industry4">

                <ul class="industry-list">
                    <li> <a href="javascript:void(0);">Retail Industry Email List</a></li>
                    <li> <a href="javascript:void(0);">Grocery Store Chains Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Retail CEOs Email List</a></li>
                    <li> <a href="javascript:void(0);">ECommerce Retailers Database</a></li>
                    <li> <a href="javascript:void(0);">Online Fashion Retail Contact List</a></li>
                    <li> <a href="javascript:void(0);">ECommerce Founders Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Apparel Retail Email List</a></li>
                    <li> <a href="javascript:void(0);">Luxury Clothing Boutiques Database</a></li>
                    <li> <a href="javascript:void(0);">Fashion Retail Owners Contact List</a></li>
                    <li> <a href="javascript:void(0);">Consumer Electronics Stores Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Mobile Phone Retailers Email List</a></li>
                    <li> <a href="javascript:void(0);">Retail Operations VPs Database</a></li>
                    <li> <a href="javascript:void(0);">Home Improvement Retail Database</a></li>
                    <li> <a href="javascript:void(0);">Hardware Store Chains Contact List</a></li>
                    <li> <a href="javascript:void(0);">Store Managers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Supermarket Retailers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Organic Food Retailers Database</a></li>
                    <li> <a href="javascript:void(0);">Grocery Store Owners Email List</a></li>
                    <li> <a href="javascript:void(0);">Beauty Supply Retail Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Cosmetics Retail Chains Email List</a></li>
                    <li> <a href="javascript:void(0);">Beauty Retail CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Furniture Retailers Email Database</a></li>
                    <li> <a href="javascript:void(0);">Office Furniture Showrooms Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Furniture Store Owners Database</a></li>
                    <li> <a href="javascript:void(0);">Automotive Parts Retail Contact List</a></li>
                    <li> <a href="javascript:void(0);">Tire Retail Dealers Email List</a></li>
                    <li> <a href="javascript:void(0);">Auto Retail Managers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Sporting Goods Retailers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Outdoor Equipment Stores Database</a></li>
                    <li> <a href="javascript:void(0);">Sporting Goods CEOs Email List</a></li>
                    <li> <a href="javascript:void(0);">Pharmacy Retail Database</a></li>
                    <li> <a href="javascript:void(0);">Independent Drug Stores Contact List</a></li>
                    <li> <a href="javascript:void(0);">Pharmacy Owners Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Pet Supply Retail Email List</a></li>
                    <li> <a href="javascript:void(0);">Specialty Pet Stores Database</a></li>
                    <li> <a href="javascript:void(0);">Pet Retail Founders Contact List</a></li>
                    <li> <a href="javascript:void(0);">Jewelry Retailers Contact Database</a></li>
                    <li> <a href="javascript:void(0);">Luxury Watch Retailers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Jewelry Store CEOs Email List</a></li>
                    <li> <a href="javascript:void(0);">Convenience Store Chains Email List</a></li>
                    <li> <a href="javascript:void(0);">Gas Station Retail Markets Database</a></li>
                    <li> <a href="javascript:void(0);">Convenience Store Managers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Department Store Retail Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Discount Retail Chains Email Database</a></li>
                    <li> <a href="javascript:void(0);">Retail Procurement VPs Contact List</a></li>
                    
                    
                </ul>

            </div>
      <!-- TAB 5 -->
       <div class="industry-tab-content" id="industry5">

            <ul class="industry-list">
                <li> <a href="javascript:void(0);">Construction Companies Email List</a></li>
                <li> <a href="javascript:void(0);">Commercial Building Contractors Database</a></li>
                <li> <a href="javascript:void(0);">Construction CEOs Contact List</a></li>
                <li> <a href="javascript:void(0);">General Contractors Mailing List</a></li>
                <li> <a href="javascript:void(0);">Residential Home Builders Email List</a></li>
                <li> <a href="javascript:void(0);">Construction Owners Mailing List</a></li>
                <li> <a href="javascript:void(0);">Engineering And Construction Database</a></li>
                <li> <a href="javascript:void(0);">Civil Engineering Firms Contact List</a></li>
                <li> <a href="javascript:void(0);">Project Managers Email Database</a></li>
                <li> <a href="javascript:void(0);">Heavy Construction Companies Contact List</a></li>
                <li> <a href="javascript:void(0);">Highway Infrastructure Contractors Mailing List</a></li>
                <li> <a href="javascript:void(0);">Construction VPs Email List</a></li>
                <li> <a href="javascript:void(0);">Real Estate Development Firms Database</a></li>
                <li> <a href="javascript:void(0);">Multifamily Housing Developers Contact List</a></li>
                <li> <a href="javascript:void(0);">Real Estate Founders Mailing List</a></li>
                <li> <a href="javascript:void(0);">Industrial Construction Email List</a></li>
                <li> <a href="javascript:void(0);">Manufacturing Plant Builders Database</a></li>
                <li> <a href="javascript:void(0);">Industrial Construction CEOs Email List</a></li>
                <li> <a href="javascript:void(0);">Roofing Contractors Mailing List</a></li>
                <li> <a href="javascript:void(0);">Metal Roofing Installers Contact List</a></li>
                <li> <a href="javascript:void(0);">Roofing Company Owners Database</a></li>
                <li> <a href="javascript:void(0);">Electrical Construction Database</a></li>
                <li> <a href="javascript:void(0);">Commercial Electrical Contractors Email List</a></li>
                <li> <a href="javascript:void(0);">Electrical Project Managers Mailing List</a></li>
                <li> <a href="javascript:void(0);">HVAC Construction Contact List</a></li>
                <li> <a href="javascript:void(0);">Mechanical Contractors Database</a></li>
                <li> <a href="javascript:void(0);">HVAC Business Owners Email List</a></li>
                <li> <a href="javascript:void(0);">Plumbing Contractors Email Database</a></li>
                <li> <a href="javascript:void(0);">Water System Installation Firms Mailing List</a></li>
                <li> <a href="javascript:void(0);">Plumbing Company CEOs Contact List</a></li>
                <li> <a href="javascript:void(0);">Concrete Construction Mailing List</a></li>
                <li> <a href="javascript:void(0);">Ready Mix Concrete Suppliers Database</a></li>
                <li> <a href="javascript:void(0);">Concrete Contractors Owners List</a></li>
                <li> <a href="javascript:void(0);">Green Building Companies Email List</a></li>
                <li> <a href="javascript:void(0);">Sustainable Construction Consultants Contact List</a></li>
                <li> <a href="javascript:void(0);">Green Construction Directors Mailing List</a></li>
                <li> <a href="javascript:void(0);">Construction Equipment Dealers Database</a></li>
                <li> <a href="javascript:void(0);">Heavy Machinery Rental Companies Email List</a></li>
                <li> <a href="javascript:void(0);">Equipment Rental Managers Contact List</a></li>
                <li> <a href="javascript:void(0);">Architecture And Construction Firms Mailing List</a></li>
                <li> <a href="javascript:void(0);">Landscape Construction Contractors Database</a></li>
                <li> <a href="javascript:void(0);">Architecture Firm Founders Email List</a></li>
                <li> <a href="javascript:void(0);">Remodeling And Renovation Contractors Contact List</a></li>
                <li> <a href="javascript:void(0);">Kitchen And Bath Remodelers Mailing List</a></li>
                <li> <a href="javascript:void(0);">Renovation Business Owners Database</a></li>
                
                
            </ul>

        </div>

      <!-- TAB 6 -->
        <div class="industry-tab-content" id="industry6">

                <ul class="industry-list">
                    <li> <a href="javascript:void(0);">Financial Services Email List</a></li>
                    <li> <a href="javascript:void(0);">Investment Banking Firms Database</a></li>
                    <li> <a href="javascript:void(0);">Finance CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Banking Industry Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Commercial Banks Email Database</a></li>
                    <li> <a href="javascript:void(0);">Banking Executives Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Insurance Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Health Insurance Providers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Insurance Founders Email List</a></li>
                    <li> <a href="javascript:void(0);">Wealth Management Firms Database</a></li>
                    <li> <a href="javascript:void(0);">Private Equity Firms Contact List</a></li>
                    <li> <a href="javascript:void(0);">Wealth Management VPs Email List</a></li>
                    <li> <a href="javascript:void(0);">Accounting Firms Email List</a></li>
                    <li> <a href="javascript:void(0);">Certified Public Accountants Database</a></li>
                    <li> <a href="javascript:void(0);">Finance Directors Contact List</a></li>
                    <li> <a href="javascript:void(0);">Mortgage Lenders Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Residential Loan Providers Email List</a></li>
                    <li> <a href="javascript:void(0);">Mortgage Company Owners Database</a></li>
                    <li> <a href="javascript:void(0);">Credit Union Database</a></li>
                    <li> <a href="javascript:void(0);">Community Credit Unions Contact List</a></li>
                    <li> <a href="javascript:void(0);">Credit Union Managers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">FinTech Companies Email Database</a></li>
                    <li> <a href="javascript:void(0);">Digital Payment Providers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">FinTech CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Venture Capital Firms Contact List</a></li>
                    <li> <a href="javascript:void(0);">Startup Investment Funds Database</a></li>
                    <li> <a href="javascript:void(0);">Venture Capital Partners Email List</a></li>
                    <li> <a href="javascript:void(0);">Tax Consulting Firms Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Corporate Tax Advisors Contact Database</a></li>
                    <li> <a href="javascript:void(0);">Tax Firm Owners Email List</a></li>
                    <li> <a href="javascript:void(0);">Asset Management Companies Email List</a></li>
                    <li> <a href="javascript:void(0);">Hedge Fund Managers Database</a></li>
                    <li> <a href="javascript:void(0);">Asset Management Executives Contact List</a></li>
                    <li> <a href="javascript:void(0);">Financial Advisory Firms Database</a></li>
                    <li> <a href="javascript:void(0);">Retirement Planning Consultants Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Financial Advisors Email List</a></li>
                    <li> <a href="javascript:void(0);">Commercial Finance Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Equipment Financing Providers Database</a></li>
                    <li> <a href="javascript:void(0);">Commercial Lending Managers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Payroll Service Providers Email List</a></li>
                    <li> <a href="javascript:void(0);">Employee Benefits Consultants Contact List</a></li>
                    <li> <a href="javascript:void(0);">Payroll Operations Directors Database</a></li>
                    <li> <a href="javascript:void(0);">Risk Management Firms Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Compliance Consulting Services Database</a></li>
                    <li> <a href="javascript:void(0);">Risk Management CEOs Email List</a></li>
                    
                    
                </ul>

            </div>
      <!-- TAB 7 -->
        <div class="industry-tab-content" id="industry7">

                <ul class="industry-list">
                    <li> <a href="javascript:void(0);">Food And Beverage Industry Email List</a></li>
                    <li> <a href="javascript:void(0);">Packaged Food Manufacturers Database</a></li>
                    <li> <a href="javascript:void(0);">Food Industry CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Restaurant Chains Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Fast Casual Restaurants Email Database</a></li>
                    <li> <a href="javascript:void(0);">Restaurant Owners Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Beverage Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Soft Drink Manufacturers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Beverage Executives Email List</a></li>
                    <li> <a href="javascript:void(0);">Food Processing Companies Database</a></li>
                    <li> <a href="javascript:void(0);">Frozen Food Producers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Food Processing Managers Database</a></li>
                    <li> <a href="javascript:void(0);">Grocery And Supermarket Email List</a></li>
                    <li> <a href="javascript:void(0);">Organic Food Retailers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Grocery Store CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Dairy Industry Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Cheese Manufacturers Email Database</a></li>
                    <li> <a href="javascript:void(0);">Dairy Plant Owners Contact List</a></li>
                    <li> <a href="javascript:void(0);">Bakery And Confectionery Database</a></li>
                    <li> <a href="javascript:void(0);">Artisan Bakery Chains Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Bakery Founders Email List</a></li>
                    <li> <a href="javascript:void(0);">Meat And Poultry Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Seafood Processing Firms Database</a></li>
                    <li> <a href="javascript:void(0);">Protein Industry VPs Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Catering Services Email Database</a></li>
                    <li> <a href="javascript:void(0);">Corporate Catering Providers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Catering Business Owners Email List</a></li>
                    <li> <a href="javascript:void(0);">Coffee And Tea Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Specialty Coffee Roasters Database</a></li>
                    <li> <a href="javascript:void(0);">Coffee Shop CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Alcohol Beverage Industry Contact List</a></li>
                    <li> <a href="javascript:void(0);">Craft Brewery Companies Email List</a></li>
                    <li> <a href="javascript:void(0);">Brewery Founders Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Food Distribution Companies Database</a></li>
                    <li> <a href="javascript:void(0);">Wholesale Food Suppliers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Distribution Managers Email Database</a></li>
                    <li> <a href="javascript:void(0);">Snack Food Manufacturers Email List</a></li>
                    <li> <a href="javascript:void(0);">Healthy Snack Brands Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Snack Food Executives Contact List</a></li>
                    <li> <a href="javascript:void(0);">Bottled Water Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Beverage Bottling Plants Database</a></li>
                    <li> <a href="javascript:void(0);">Bottling Operations Directors Email List</a></li>
                    <li> <a href="javascript:void(0);">Commercial Kitchen Suppliers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Restaurant Equipment Dealers Database</a></li>
                    <li> <a href="javascript:void(0);">Food Service Procurement Managers Mailing List</a></li>
                    
                    
                </ul>

            </div>
      <!-- TAB 8 -->
        <div class="industry-tab-content" id="industry8">

                <ul class="industry-list">
                    <li> <a href="javascript:void(0);">Education Industry Email List</a></li>
                    <li> <a href="javascript:void(0);">Higher Education Institutions Database</a></li>
                    <li> <a href="javascript:void(0);">Education CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Schools And Universities Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Private K12 Schools Email Database</a></li>
                    <li> <a href="javascript:void(0);">School Administrators Mailing List</a></li>
                    <li> <a href="javascript:void(0);">EdTech Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">Online Learning Platforms Mailing List</a></li>
                    <li> <a href="javascript:void(0);">EdTech Founders Email List</a></li>
                    <li> <a href="javascript:void(0);">Colleges And Universities Database</a></li>
                    <li> <a href="javascript:void(0);">Community Colleges Contact List </a></li>
                    <li> <a href="javascript:void(0);">University Presidents Email List</a></li>
                    <li> <a href="javascript:void(0);">Training And Development Firms Email List</a></li>
                    <li> <a href="javascript:void(0);">Corporate Training Providers Database</a></li>
                    <li> <a href="javascript:void(0);">Training Managers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Educational Services Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Tutoring Centers Email Database</a></li>
                    <li> <a href="javascript:void(0);">Education Business Owners Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Academic Institutions Contact List</a></li>
                    <li> <a href="javascript:void(0);">STEM Education Programs Database</a></li>
                    <li> <a href="javascript:void(0);">Academic Directors Email List</a></li>
                    <li> <a href="javascript:void(0);">Early Childhood Education Database</a></li>
                    <li> <a href="javascript:void(0);">Preschool And Daycare Centers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Childcare Owners Contact List</a></li>
                    <li> <a href="javascript:void(0);">Vocational Schools Email List</a></li>
                    <li> <a href="javascript:void(0);">Technical Training Institutes Database</a></li>
                    <li> <a href="javascript:void(0);">Vocational School CEOs Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Student Recruitment Firms Contact List</a></li>
                    <li> <a href="javascript:void(0);">International Education Consultants Email List</a></li>
                    <li> <a href="javascript:void(0);">Admissions Directors Database</a></li>
                    <li> <a href="javascript:void(0);">E Learning Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Virtual Classroom Providers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Learning Platform Executives Email List</a></li>
                    <li> <a href="javascript:void(0);">Educational Publishers Database</a></li>
                    <li> <a href="javascript:void(0);">Digital Textbook Providers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Publishing Managers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Charter Schools Email Database</a></li>
                    <li> <a href="javascript:void(0);">Montessori Schools Contact List</a></li>
                    <li> <a href="javascript:void(0);">School Founders Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Education Nonprofit Organizations Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Scholarship Program Providers Database</a></li>
                    <li> <a href="javascript:void(0);">Nonprofit Education Directors Email List</a></li>
                    <li> <a href="javascript:void(0);">Campus Technology Providers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Student Information System Vendors Database</a></li>
                    <li> <a href="javascript:void(0);">Education IT Managers Mailing List</a></li>
                    
                    
                </ul>

            </div>
      <!-- TAB 9 -->
        <div class="industry-tab-content" id="industry9">

                <ul class="industry-list">
                    <li> <a href="javascript:void(0);">Automotive Industry Email List</a></li>
                    <li> <a href="javascript:void(0);">Auto Dealerships Database</a></li>
                    <li> <a href="javascript:void(0);">Automotive CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Car Dealers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Luxury Vehicle Dealers Email Database</a></li>
                    <li> <a href="javascript:void(0);">Dealership Owners Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Auto Parts Manufacturers Contact List</a></li>
                    <li> <a href="javascript:void(0);">OEM Automotive Suppliers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Automotive VPs Email List</a></li>
                    <li> <a href="javascript:void(0);">Vehicle Repair Services Database</a></li>
                    <li> <a href="javascript:void(0);">Collision Repair Centers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Auto Service Managers Database</a></li>
                    <li> <a href="javascript:void(0);">Automotive Technology Companies Email List</a></li>
                    <li> <a href="javascript:void(0);">Connected Car Solutions Providers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Automotive Founders Contact List</a></li>
                    <li> <a href="javascript:void(0);">Trucking And Fleet Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Commercial Vehicle Operators Database</a></li>
                    <li> <a href="javascript:void(0);">Fleet Managers Email List</a></li>
                    <li> <a href="javascript:void(0);">Electric Vehicle Companies Contact List</a></li>
                    <li> <a href="javascript:void(0);">EV Charging Station Providers Database</a></li>
                    <li> <a href="javascript:void(0);">EV Industry Executives Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Tire Manufacturers Email Database</a></li>
                    <li> <a href="javascript:void(0);">Tire Retail Chains Contact List</a></li>
                    <li> <a href="javascript:void(0);">Tire Company Owners Email List</a></li>
                    <li> <a href="javascript:void(0);">Auto Finance Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Vehicle Leasing Providers Database</a></li>
                    <li> <a href="javascript:void(0);">Automotive Finance Directors Contact List</a></li>
                    <li> <a href="javascript:void(0);">Automotive Aftermarket Suppliers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Performance Parts Manufacturers Email List</a></li>
                    <li> <a href="javascript:void(0);">Aftermarket Sales Managers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Motorcycle Dealers Database</a></li>
                    <li> <a href="javascript:void(0);">Powersports Retailers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Motorcycle Business Owners Email List</a></li>
                    <li> <a href="javascript:void(0);">Car Rental Companies Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Luxury Car Rental Providers Database</a></li>
                    <li> <a href="javascript:void(0);">Rental Operations Managers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Automotive Logistics Firms Email List</a></li>
                    <li> <a href="javascript:void(0);">Vehicle Transport Services Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Logistics Directors Database</a></li>
                    <li> <a href="javascript:void(0);">Auto Body Shops Contact Database</a></li>
                    <li> <a href="javascript:void(0);">Paint And Refinishing Contractors Email List</a></li>
                    <li> <a href="javascript:void(0);">Collision Center CEOs Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Automotive Software Providers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Dealer Management System Vendors Database</a></li>
                    <li> <a href="javascript:void(0);">Automotive IT Managers Contact List</a></li>
                    
                    
                </ul>

            </div>
      <!-- TAB 10 -->
        <div class="industry-tab-content" id="industry10">

                <ul class="industry-list">
                   <li> <a href="javascript:void(0);">Logistics Companies Email List	</a></li>
                    <li> <a href="javascript:void(0);">Third Party Logistics Providers Database	</a></li>
                    <li> <a href="javascript:void(0);">Logistics CEOs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Transportation Services Mailing List	</a></li>
                    <li> <a href="javascript:void(0);">Freight Forwarding Companies Email Database	</a></li>
                    <li> <a href="javascript:void(0);">Transportation Managers Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Supply Chain Companies Contact List	</a></li>
                    <li> <a href="javascript:void(0);">Warehouse Distribution Centers Mailing List	</a></li>
                    <li> <a href="javascript:void(0);">Supply Chain Executives Email List</a></li>
                    <li> <a href="javascript:void(0);">Trucking Companies Database	</a></li>
                    <li> <a href="javascript:void(0);">Long Haul Trucking Fleets Contact List	</a></li>
                    <li> <a href="javascript:void(0);">Fleet Owners Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Freight And Shipping Email List</a></li>
                    <li> <a href="javascript:void(0);">Ocean Freight Carriers Database	</a></li>
                    <li> <a href="javascript:void(0);">Shipping Directors Contact List</a></li>
                    <li> <a href="javascript:void(0);">Warehousing Services Mailing List</a></li>	
                    <li> <a href="javascript:void(0);">Cold Storage Facilities Email Database</a></li>	
                    <li> <a href="javascript:void(0);">Warehouse Managers Contact List</a></li>
                    <li> <a href="javascript:void(0);">Courier And Delivery Companies Contact List	</a></li>
                    <li> <a href="javascript:void(0);">Last Mile Delivery Providers Database	</a></li>
                    <li> <a href="javascript:void(0);">Delivery Operations CEOs Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Air Cargo Services Email Database	</a></li>
                    <li> <a href="javascript:void(0);">Aviation Logistics Providers Contact List</a></li>	
                    <li> <a href="javascript:void(0);">Air Freight Managers Email List</a></li>
                    <li> <a href="javascript:void(0);">Rail Logistics Companies Mailing List	</a></li>
                    <li> <a href="javascript:void(0);">Intermodal Transport Providers Database	</a></li>
                    <li> <a href="javascript:void(0);">Rail Logistics Executives Contact List</a></li>
                    <li> <a href="javascript:void(0);">Ecommerce Fulfillment Centers Contact List	</a></li>
                    <li> <a href="javascript:void(0);">Order Fulfillment Providers Email List	</a></li>
                    <li> <a href="javascript:void(0);">Fulfillment Operations Managers Database</a></li>
                    <li> <a href="javascript:void(0);">Import Export Companies Mailing List	</a></li>
                    <li> <a href="javascript:void(0);">Customs Brokerage Firms Database	</a></li>
                    <li> <a href="javascript:void(0);">Customs Compliance Directors Email List</a></li>
                    <li> <a href="javascript:void(0);">Fleet Management Services Contact Database	</a></li>
                    <li> <a href="javascript:void(0);">Commercial Vehicle Tracking Providers Mailing List	</a></li>
                    <li> <a href="javascript:void(0);">Fleet Operations VPs Contact List</a></li>
                    <li> <a href="javascript:void(0);">Maritime Logistics Companies Email List	</a></li>
                    <li> <a href="javascript:void(0);">Port Terminal Operators Database	</a></li>
                    <li> <a href="javascript:void(0);">Maritime CEOs Mailing List</a></li>
                    <li> <a href="javascript:void(0);">Reverse Logistics Providers Mailing List	</a></li>
                    <li> <a href="javascript:void(0);">Returns Management Services Contact List	</a></li>
                    <li> <a href="javascript:void(0);">Reverse Logistics Managers Email Database</a></li>
                    <li> <a href="javascript:void(0);">Logistics Technology Companies Contact List</a></li>	
                    <li> <a href="javascript:void(0);">Transportation Management Software Vendors Database	</a></li>
                    <li> <a href="javascript:void(0);">Logistics IT Directors Mailing List</a></li>
                </ul>

            </div>
      <!-- TAB 11 -->
        <div class="industry-tab-content" id="industry11">

                <ul class="industry-list">
                    <li><a href="javascript:void(0);">Business Consulting Firms Email List</a></li>
                    <li><a href="javascript:void(0);">Management Consulting Companies Database</a></li>
                    <li><a href="javascript:void(0);">Consulting CEOs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Corporate Advisory Services Mailing List</a></li>
                    <li><a href="javascript:void(0);">Strategy Consulting Firms Email Database</a></li>
                    <li><a href="javascript:void(0);">Business Consultants Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Financial Consulting Companies Contact List</a></li>
                    <li><a href="javascript:void(0);">Risk Management Consultants Mailing List</a></li>
                    <li><a href="javascript:void(0);">Consulting Founders Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Human Resources Consulting Database</a></li>
                    <li><a href="javascript:void(0);">Talent Acquisition Consulting Firms Contact List</a></li>
                    <li><a href="javascript:void(0);">HR Consulting Directors Email List</a></li>
                    
                    <li><a href="javascript:void(0);">IT Consulting Services Email List</a></li>
                    <li><a href="javascript:void(0);">Digital Transformation Consultants Database</a></li>
                    <li><a href="javascript:void(0);">IT Consulting VPs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Marketing Consulting Firms Mailing List</a></li>
                    <li><a href="javascript:void(0);">Brand Strategy Consultants Email Database</a></li>
                    <li><a href="javascript:void(0);">Marketing Consulting Owners Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Operations Consulting Companies Contact List</a></li>
                    <li><a href="javascript:void(0);">Supply Chain Consulting Firms Database</a></li>
                    <li><a href="javascript:void(0);">Operations Managers Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Healthcare Consulting Services Email Database</a></li>
                    <li><a href="javascript:void(0);">Medical Practice Consultants Contact List</a></li>
                    <li><a href="javascript:void(0);">Healthcare Consulting CEOs Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Legal Consulting Firms Mailing List</a></li>
                    <li><a href="javascript:void(0);">Compliance Advisory Services Database</a></li>
                    <li><a href="javascript:void(0);">Compliance Consultants Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Environmental Consulting Companies Contact List</a></li>
                    <li><a href="javascript:void(0);">Sustainability Consulting Firms Email Database</a></li>
                    <li><a href="javascript:void(0);">Environmental Consulting Directors Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Executive Coaching Services Database</a></li>
                    <li><a href="javascript:void(0);">Leadership Development Consultants Contact List</a></li>
                    <li><a href="javascript:void(0);">Executive Coaches Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Startup Consulting Firms Mailing List</a></li>
                    <li><a href="javascript:void(0);">Small Business Advisory Services Database</a></li>
                    <li><a href="javascript:void(0);">Startup Advisors Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Engineering Consulting Companies Email List</a></li>
                    <li><a href="javascript:void(0);">Infrastructure Consulting Firms Mailing List</a></li>
                    <li><a href="javascript:void(0);">Engineering Consulting Managers Database</a></li>
                    
                    <li><a href="javascript:void(0);">Retail Consulting Services Contact Database</a></li>
                    <li><a href="javascript:void(0);">Ecommerce Strategy Consultants Email List</a></li>
                    <li><a href="javascript:void(0);">Retail Consulting Executives Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Business Analytics Consulting Firms Mailing List</a></li>
                    <li><a href="javascript:void(0);">Data Intelligence Consultants Database</a></li>
                    <li><a href="javascript:void(0);">Analytics Consulting Founders Contact List</a></li>
                    
                </ul>

            </div>
      <!-- TAB 12 -->
        <div class="industry-tab-content" id="industry12">

               
                <ul class="industry-list">
                    <li><a href="javascript:void(0);">Telecommunications Companies Email List</a></li>
                    <li><a href="javascript:void(0);">Wireless Network Providers Database</a></li>
                    <li><a href="javascript:void(0);">Telecom CEOs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Telecom Service Providers Mailing List</a></li>
                    <li><a href="javascript:void(0);">Mobile Carrier Companies Email Database</a></li>
                    <li><a href="javascript:void(0);">Telecommunications Executives Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Internet Service Providers Contact List</a></li>
                    <li><a href="javascript:void(0);">Fiber Optic Network Operators Mailing List</a></li>
                    <li><a href="javascript:void(0);">ISP Owners Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Telecom Infrastructure Companies Database</a></li>
                    <li><a href="javascript:void(0);">Cell Tower Management Firms Contact List</a></li>
                    <li><a href="javascript:void(0);">Telecom Operations Managers Email List</a></li>
                    
                    <li><a href="javascript:void(0);">VoIP Service Providers Email List</a></li>
                    <li><a href="javascript:void(0);">Cloud Communications Platforms Mailing List</a></li>
                    <li><a href="javascript:void(0);">VoIP Founders Database</a></li>
                    
                    <li><a href="javascript:void(0);">Cable And Satellite Companies Contact List</a></li>
                    <li><a href="javascript:void(0);">Broadband Service Providers Database</a></li>
                    <li><a href="javascript:void(0);">Cable Industry VPs Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Unified Communications Companies Email Database</a></li>
                    <li><a href="javascript:void(0);">Business Phone System Providers Contact List</a></li>
                    <li><a href="javascript:void(0);">Communications Directors Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Telecom Equipment Manufacturers Mailing List</a></li>
                    <li><a href="javascript:void(0);">Network Hardware Suppliers Database</a></li>
                    <li><a href="javascript:void(0);">Telecom Product Managers Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Data Center Connectivity Providers Contact List</a></li>
                    <li><a href="javascript:void(0);">Colocation Network Services Email List</a></li>
                    <li><a href="javascript:void(0);">Data Network Executives Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">5G Technology Companies Database</a></li>
                    <li><a href="javascript:void(0);">Small Cell Deployment Providers Mailing List</a></li>
                    <li><a href="javascript:void(0);">5G Innovation CEOs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Telecom Software Vendors Email List</a></li>
                    <li><a href="javascript:void(0);">OSS BSS Solution Providers Database</a></li>
                    <li><a href="javascript:void(0);">Telecom IT Managers Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Satellite Communications Companies Mailing List</a></li>
                    <li><a href="javascript:void(0);">Remote Connectivity Providers Contact List</a></li>
                    <li><a href="javascript:void(0);">Satellite Network Directors Database</a></li>
                    
                    <li><a href="javascript:void(0);">Enterprise Networking Firms Contact Database</a></li>
                    <li><a href="javascript:void(0);">SD WAN Service Providers Mailing List</a></li>
                    <li><a href="javascript:void(0);">Enterprise Telecom Managers Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Telecommunications Consulting Firms Email List</a></li>
                    <li><a href="javascript:void(0);">Network Security Consultants Database</a></li>
                    <li><a href="javascript:void(0);">Telecom Consulting Founders Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Telecom Resellers And Distributors Contact List</a></li>
                    <li><a href="javascript:void(0);">Managed Communication Services Database</a></li>
                    <li><a href="javascript:void(0);">Channel Partner Managers Email List</a></li>
                    
                </ul>


            </div>

      <!-- TAB 13 -->
        <div class="industry-tab-content" id="industry13">
 <ul class="industry-list">
                    <li><a href="javascript:void(0);">Consumer Goods Companies Email List</a></li>
                    <li><a href="javascript:void(0);">Household Products Manufacturers Database</a></li>
                    <li><a href="javascript:void(0);">Consumer Goods CEOs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Fast Moving Consumer Goods Mailing List</a></li>
                    <li><a href="javascript:void(0);">Packaged Consumer Brands Email Database</a></li>
                    <li><a href="javascript:void(0);">FMCG Executives Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Personal Care Products Contact List</a></li>
                    <li><a href="javascript:void(0);">Skincare Brands Database</a></li>
                    <li><a href="javascript:void(0);">Beauty Brand Founders Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Home And Kitchen Products Database</a></li>
                    <li><a href="javascript:void(0);">Kitchenware Manufacturers Contact List</a></li>
                    <li><a href="javascript:void(0);">Product Development Managers Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Apparel Consumer Brands Email List</a></li>
                    <li><a href="javascript:void(0);">Footwear Manufacturers Mailing List</a></li>
                    <li><a href="javascript:void(0);">Fashion Brand Owners Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Consumer Electronics Companies Database</a></li>
                    <li><a href="javascript:void(0);">Smart Home Device Manufacturers Email List</a></li>
                    <li><a href="javascript:void(0);">Electronics CEOs Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Health And Wellness Brands Contact List</a></li>
                    <li><a href="javascript:void(0);">Nutritional Supplement Companies Database</a></li>
                    <li><a href="javascript:void(0);">Wellness Brand Managers Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Toys And Games Manufacturers Mailing List</a></li>
                    <li><a href="javascript:void(0);">Educational Toy Brands Contact List</a></li>
                    <li><a href="javascript:void(0);">Toy Industry Executives Database</a></li>
                    
                    <li><a href="javascript:void(0);">Pet Consumer Goods Email Database</a></li>
                    <li><a href="javascript:void(0);">Pet Accessories Manufacturers Mailing List</a></li>
                    <li><a href="javascript:void(0);">Pet Brand Founders Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Luxury Consumer Brands Contact List</a></li>
                    <li><a href="javascript:void(0);">Premium Lifestyle Products Database</a></li>
                    <li><a href="javascript:void(0);">Luxury Brand Directors Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Cleaning Products Companies Mailing List</a></li>
                    <li><a href="javascript:void(0);">Eco Friendly Household Brands Database</a></li>
                    <li><a href="javascript:void(0);">Cleaning Product Owners Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Baby Products Manufacturers Email List</a></li>
                    <li><a href="javascript:void(0);">Infant Care Brands Mailing List</a></li>
                    <li><a href="javascript:void(0);">Baby Brand CEOs Database</a></li>
                    
                    <li><a href="javascript:void(0);">Outdoor Consumer Goods Contact Database</a></li>
                    <li><a href="javascript:void(0);">Camping Gear Manufacturers Email List</a></li>
                    <li><a href="javascript:void(0);">Outdoor Product Managers Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Beverage Consumer Brands Mailing List</a></li>
                    <li><a href="javascript:void(0);">Functional Drink Companies Database</a></li>
                    <li><a href="javascript:void(0);">Beverage Brand Executives Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Consumer Goods Distributors Email List</a></li>
                    <li><a href="javascript:void(0);">Wholesale Consumer Product Suppliers Database</a></li>
                    <li><a href="javascript:void(0);">Distribution VPs Mailing List</a></li>
                    
                    
                </ul>
            </div>
      <!-- TAB 14 -->
        <div class="industry-tab-content" id="industry14">

                <ul class="industry-list">
                    <li><a href="javascript:void(0);">Oil And Gas Companies Email List</a></li>
                    <li><a href="javascript:void(0);">Upstream Exploration Firms Database</a></li>
                    <li><a href="javascript:void(0);">Oil And Gas CEOs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Petroleum Industry Mailing List</a></li>
                    <li><a href="javascript:void(0);">Offshore Drilling Contractors Email Database</a></li>
                    <li><a href="javascript:void(0);">Energy Executives Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Natural Gas Companies Contact List</a></li>
                    <li><a href="javascript:void(0);">LNG Processing Facilities Mailing List</a></li>
                    <li><a href="javascript:void(0);">Gas Operations Managers Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Oilfield Services Database</a></li>
                    <li><a href="javascript:void(0);">Hydraulic Fracturing Providers Contact List</a></li>
                    <li><a href="javascript:void(0);">Oilfield Service Owners Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Refining And Petrochemical Companies Email List</a></li>
                    <li><a href="javascript:void(0);">Crude Oil Refineries Database</a></li>
                    <li><a href="javascript:void(0);">Refinery Directors Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Pipeline Operators Mailing List</a></li>
                    <li><a href="javascript:void(0);">Midstream Transportation Companies Contact Database</a></li>
                    <li><a href="javascript:void(0);">Pipeline Managers Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Energy Infrastructure Companies Email Database</a></li>
                    <li><a href="javascript:void(0);">Storage Terminal Operators Mailing List</a></li>
                    <li><a href="javascript:void(0);">Infrastructure VPs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Drilling Equipment Manufacturers Contact List</a></li>
                    <li><a href="javascript:void(0);">Oil Rig Equipment Suppliers Database</a></li>
                    <li><a href="javascript:void(0);">Equipment Procurement Managers Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Energy Consulting Firms Mailing List</a></li>
                    <li><a href="javascript:void(0);">Reservoir Engineering Consultants Database</a></li>
                    <li><a href="javascript:void(0);">Energy Consulting Founders Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Fuel Distribution Companies Contact Database</a></li>
                    <li><a href="javascript:void(0);">Wholesale Petroleum Suppliers Email List</a></li>
                    <li><a href="javascript:void(0);">Fuel Distribution CEOs Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Offshore Energy Companies Email List</a></li>
                    <li><a href="javascript:void(0);">Subsea Engineering Contractors Mailing List</a></li>
                    <li><a href="javascript:void(0);">Offshore Operations Directors Database</a></li>
                    
                    <li><a href="javascript:void(0);">Oil And Gas Technology Providers Contact List</a></li>
                    <li><a href="javascript:void(0);">Energy Analytics Software Vendors Database</a></li>
                    <li><a href="javascript:void(0);">Energy IT Managers Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Environmental Energy Services Mailing List</a></li>
                    <li><a href="javascript:void(0);">Oil Spill Response Contractors Database</a></li>
                    <li><a href="javascript:void(0);">HSE Managers Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Industrial Lubricant Manufacturers Email Database</a></li>
                    <li><a href="javascript:void(0);">Specialty Oil Product Suppliers Contact List</a></li>
                    <li><a href="javascript:void(0);">Lubricant Brand Executives Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Renewable Energy Transition Firms Contact List</a></li>
                    <li><a href="javascript:void(0);">Carbon Capture Technology Providers Database</a></li>
                    <li><a href="javascript:void(0);">Energy Innovation Leaders Email List</a></li>
                    
                </ul>

            </div>
      <!-- TAB 15 -->
        <div class="industry-tab-content" id="industry15">

                <ul class="industry-list">
                    <li><a href="javascript:void(0);">Aviation Industry Email List</a></li>
                    <li><a href="javascript:void(0);">Commercial Airlines Database</a></li>
                    <li><a href="javascript:void(0);">Aviation CEOs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Aerospace Companies Mailing List</a></li>
                    <li><a href="javascript:void(0);">Aircraft Manufacturers Email Database</a></li>
                    <li><a href="javascript:void(0);">Aerospace Executives Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Airport Operations Contact List</a></li>
                    <li><a href="javascript:void(0);">International Airport Authorities Database</a></li>
                    <li><a href="javascript:void(0);">Airport Managers Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Aviation Maintenance Companies Email List</a></li>
                    <li><a href="javascript:void(0);">Aircraft MRO Providers Mailing List</a></li>
                    <li><a href="javascript:void(0);">Maintenance Directors Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Private Aviation Firms Database</a></li>
                    <li><a href="javascript:void(0);">Business Jet Operators Contact List</a></li>
                    <li><a href="javascript:void(0);">Private Aviation Owners Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Air Cargo Companies Mailing List</a></li>
                    <li><a href="javascript:void(0);">Freight Aviation Services Database</a></li>
                    <li><a href="javascript:void(0);">Cargo Operations Managers Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Flight Training Schools Contact List</a></li>
                    <li><a href="javascript:void(0);">Pilot Training Academies Email Database</a></li>
                    <li><a href="javascript:void(0);">Aviation School Founders Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Aviation Technology Providers Database</a></li>
                    <li><a href="javascript:void(0);">Flight Software Vendors Mailing List</a></li>
                    <li><a href="javascript:void(0);">Aviation IT Managers Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Aircraft Parts Suppliers Email List</a></li>
                    <li><a href="javascript:void(0);">Avionics Equipment Manufacturers Database</a></li>
                    <li><a href="javascript:void(0);">Procurement Executives Mailing List</a></li>
                    
                    <li><a href="javascript:void(0);">Helicopter Services Companies Contact List</a></li>
                    <li><a href="javascript:void(0);">Emergency Air Transport Providers Database</a></li>
                    <li><a href="javascript:void(0);">Helicopter Operations Directors Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Airline Catering Services Mailing List</a></li>
                    <li><a href="javascript:void(0);">In Flight Food Suppliers Contact Database</a></li>
                    <li><a href="javascript:void(0);">Catering Managers Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Aviation Consulting Firms Email Database</a></li>
                    <li><a href="javascript:void(0);">Airport Infrastructure Consultants Mailing List</a></li>
                    <li><a href="javascript:void(0);">Aviation Consulting CEOs Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Drone Technology Companies Contact List</a></li>
                    <li><a href="javascript:void(0);">Commercial UAV Operators Database</a></li>
                    <li><a href="javascript:void(0);">Drone Industry Founders Email List</a></li>
                    
                    <li><a href="javascript:void(0);">Aviation Fuel Suppliers Mailing List</a></li>
                    <li><a href="javascript:void(0);">Jet Fuel Distribution Companies Database</a></li>
                    <li><a href="javascript:void(0);">Fuel Operations Managers Contact List</a></li>
                    
                    <li><a href="javascript:void(0);">Airline Staffing Agencies Email List</a></li>
                    <li><a href="javascript:void(0);">Aviation Recruitment Firms Mailing List</a></li>
                    <li><a href="javascript:void(0);">Aviation Recruiters Contact Database</a></li>
                    
                </ul>

            </div>

        </div>

    </div>

</div>

<!-- TAB SCRIPT -->
<div class="green-banner">

    <div class="banner-icon">
        ✓
    </div>

    <div class="banner-text">
        Get Instant Access to {{$service_item->data_counts}} Verified Dermatologist contact list
    </div>
<a href="https://app.go4database.com/login?utm_source=ProductPage&amp;utm_medium=Internal&amp;utm_campaign=app_login" class="read-btn" target="_blank">
   Have you requested for free sample data yet?</a>
</div>


<!-- FAQ -->
<!-- FAQ SECTION -->

<div class="faq-section">

    <h2 class="faq-title">
        Frequently Asked Questions
    </h2>

    <p class="faq-subtitle">
        You might have these questions in your mind?
    </p>


<div class="faq-wrapper">

    @if(!empty($service_item->faqs))

        @foreach($service_item->faqs as $key => $faq)

            <div class="faq-item {{ $key == 0 ? 'active' : '' }}">

                <button class="faq-question">

                    <h3>
                        {{ $faq['question'] }}
                    </h3>

                    <span class="faq-icon">+</span>

                </button>

                <div class="faq-answer">

                    <div class="faq-answer-content">

                        {{ $faq['answer'] }}

                    </div>

                </div>

            </div>

        @endforeach

    @else

        <p>No FAQ Found</p>

    @endif

</div>
</div>

<!-- FAQ SCRIPT -->

<!-- SAMPLE DATA -->

<div class="sample-data-section" id="sample-data-section">

    <div class="sample-box">

        <div class="sample-flex">

            <!-- LEFT -->

            <div class="sample-left">

                <h2>
                    Have you requested for free sample data yet?
                </h2>

                <p>
                    Lorem ipsum dolor sit amet,
                    consectetur adipiscing elit,
                    sed do eiusmod tempor incididunt
                    ut labore et dolore.
                </p>

            </div>

            <!-- RIGHT -->

            <div class="sample-right">
@if(session('sample-success'))
    <div class="alert alert-success">
        {{ session('sample-success') }}
    </div>
@endif
              <form action="{{ route('sample-lead.submit') }}" method="POST">
    @csrf

    <div class="sample-form-grid">

        <input type="text"
               name="fname"
               placeholder="Enter your first name"
               required>

        <input type="text"
               name="lname"
               placeholder="Enter your last name"
               required>

        <input type="email"
               name="business-email"
               placeholder="Business email"
               required>

        <textarea name="sample-message"
                  placeholder="What outcome are you seeking from this list?"
                  required></textarea>

        <input type="text"
               name="sample-phone"
               placeholder="Phone"
               required>

    </div>

    <button type="submit" class="sample-btn">
        Submit Your Request
    </button>
</form>

            </div>

        </div>

    </div>

</div>

<!-- BLOGS -->
<section class="related-blogs py-5">

    <div class="container">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="blog-title">Related Blogs</h2>
            </div>

            <div>
                <a class="carousel-btn mr-2"
                   href="#blogCarousel"
                   role="button"
                   data-slide="prev">
                    &#8249;
                </a>

                <a class="carousel-btn active"
                   href="#blogCarousel"
                   role="button"
                   data-slide="next">
                    &#8250;
                </a>
            </div>

        </div>

        <!-- CAROUSEL -->
        <div id="blogCarousel"
             class="carousel slide"
             data-ride="carousel">

 <div class="carousel-inner">

    @foreach($relatedBlogs->chunk(3) as $key => $blogs)
        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">

            <div class="row">

                @foreach($blogs as $blog)
                
                    <div class="col-md-4">

                        <div class="blog-card">

                            <div class="blog-img">
  {!! render_image_markup_by_attachment_id($blog->image) !!}
                                
                                <span class="blog-date">
                                    {{ date('d M, Y', strtotime($blog->created_at)) }}
                                </span>

                            </div>

                            <div class="blog-content">

                                <h3>
                                    {{ $blog->title }}
                                </h3>

                                <p>
                                    {{ \Illuminate\Support\Str::limit(strip_tags($blog->content), 120) }}
                                </p>

                                <a href="{{ route('frontend.blog.single', $blog->slug) }}"
                                   class="read-btn">
                                    Read More
                                </a>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>
    @endforeach

</div>

        </div>

    </div>

</section>

</div>
<script>

const industryTabs =
document.querySelectorAll('.industry-tab-btn');

const industryContents =
document.querySelectorAll('.industry-tab-content');

industryTabs.forEach(tab => {

    tab.addEventListener('click', () => {

        industryTabs.forEach(btn => {
            btn.classList.remove('active');
        });

        industryContents.forEach(content => {
            content.classList.remove('active');
        });

        tab.classList.add('active');

        document.getElementById(
            tab.dataset.tab
        ).classList.add('active');

    });

});

</script>
<script>

const faqItems =
document.querySelectorAll('.faq-item');

faqItems.forEach(item => {

    const question =
    item.querySelector('.faq-question');

    question.addEventListener('click', () => {

        if(item.classList.contains('active')){

            item.classList.remove('active');

        }else{

            faqItems.forEach(faq => {
                faq.classList.remove('active');
            });

            item.classList.add('active');

        }

    });

});

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    fetchLeads();
});

function fetchLeads() {

    let title = document.getElementById('search-title').value;
    let industry = document.getElementById('search-industry').value;
    let business = document.getElementById('search-business').value;
    let location = document.getElementById('search-location').value;

    fetch(`https://app.go4database.com/api/getleads?industry=${encodeURIComponent(industry)}&title=${encodeURIComponent(title)}&business=${encodeURIComponent(business)}&location=${encodeURIComponent(location)}`)
    .then(res => res.json())
    .then(data => {
        let rows = '';

        data.forEach(lead => {
            rows += `
                <tr>
                    <td>${lead.title}</td>
                    <td>${lead.company} <span class="small test">Founded : ${lead.founded_year} , Turnover ${lead.turnover}</span></td>
                    <td>${lead.person_name}</td>
                    <td> <button class="verify-email-btn view-btn" data-email='${lead.email}'  data-id='${lead.id}' data-type="email">View Email</button></td>
                    <td>${lead.industry}</td>
                </tr>
            `;
        });

        document.querySelector('#leadsTable tbody').innerHTML = rows;
    })
    .catch(err => console.log(err));
}
</script>
<script>
document.addEventListener('click', function(e) {

    if (e.target.classList.contains('verify-email-btn')) {

        let button = e.target;
        let email = button.dataset.email;

        // Replace button with email
        button.outerHTML = `<span class="email-text">${email}</span>`;
    }

});
</script>
<script>
let currentIndex = 0;
const slides = document.querySelectorAll(".testimonial-slide");

function showSlide(index) {
  slides.forEach(slide => slide.classList.remove("active"));
  slides[index].classList.add("active");
}

function nextSlide() {
  currentIndex = (currentIndex + 1) % slides.length;
  showSlide(currentIndex);
}

function prevSlide() {
  currentIndex = (currentIndex - 1 + slides.length) % slides.length;
  showSlide(currentIndex);
}
</script>
@if(session('sample-success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('sample-data-section')
        .scrollIntoView({ behavior: 'smooth' });
});
</script>

@endif
@endsection
