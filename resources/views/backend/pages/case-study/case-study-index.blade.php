@extends('backend.admin-master')
@section('style')
    <link rel="stylesheet" href="{{asset('assets/backend/css/media-uploader.css')}}">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button{
            padding: 0 !important;
        }
        div.dataTables_wrapper div.dataTables_length select {
            width: 60px;
            display: inline-block;
        }
     </style>
@endsection 
@section('site-title')
    {{__('Case Study')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12 padding-bottom-30">
        <div class="row">
            <div class="col-lg-12">
                <div class="margin-top-40"></div>
                <x-error-msg/>
                <x-flash-msg/>
            </div>

            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">{{__('Case Study Items')}}</h4>
                        <div class="bulk-delete-wrapper">
                            <div class="select-box-wrap">
                                <select name="bulk_option" id="bulk_option">
                                    <option value="">{{{__('Bulk Action')}}}</option>
                                    <option value="delete">{{{__('Delete')}}}</option>
                                </select>
                                <button class="btn btn-primary btn-sm" id="bulk_delete_btn">{{__('Apply')}}</button>
                            </div>
                        </div>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @php $a=0; @endphp
                            @foreach($all_casestudy as $key => $work)
                                <li class="nav-item">
                                    <a class="nav-link @if($a == 0) active @endif"  data-toggle="tab" href="#slider_tab_{{$key}}" role="tab" aria-controls="home" aria-selected="true">{{get_language_by_slug($key)}}</a>
                                </li>
                                @php $a++; @endphp
                            @endforeach
                        </ul>
                        <div class="tab-content margin-top-40" id="myTabContent">
                            @php $b=0; @endphp
                            @foreach($all_casestudy as $key => $work)
                                <div class="tab-pane fade @if($b == 0) show active @endif" id="slider_tab_{{$key}}" role="tabpanel" >
                                    <div class="table-wrap table-responsive">
                                        <div class="d-flex align-items-center mb-3" style="gap:10px; flex-wrap:wrap;">

    <input type="text" id="search" placeholder="Search keyword..." 
           class="form-control" style="max-width:250px;">

    <button class="btn btn-warning" data-toggle="modal" data-target="#replaceUrlModal">
        Replace URLs
    </button>


    <button id="startScanBtn" class="btn btn-danger">
    Scan Entire Website (404)
</button>

<div id="scanBox" style="display:none; margin-top:20px;">
    
    <div class="progress">
        <div id="progressBar"
             class="progress-bar progress-bar-striped progress-bar-animated"
             style="width:0%">
            0%
        </div>
    </div>

    <p id="scanStatus" class="mt-2 text-info">Starting scan...</p>

</div>
<!--<a href="{{ route('admin.scan.full') }}" class="btn btn-danger">-->
<!--    Scan Entire Website (404)-->
<!--</a>-->

<a href="{{ route('admin.scan.results') }}" class="btn btn-primary">
    View Results
</a>
</div>
                                        <table class="table table-default" id="casestudyTable">
                                        <thead>
                                        <th class="no-sort">
                                            <div class="mark-all-checkbox">
                                                <input type="checkbox" class="all-checkbox">
                                            </div>
                                        </th>
                                        <th>{{__('ID')}}</th>
                                        <th>{{__('Title')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>{{__('Image')}}</th>
                                        <th>{{__('Category')}}</th>
                                        <th>{{__('Date')}}</th>
                                        <th>{{__('Action')}}</th>
                                        </thead>
                                       
                                    </table>
                                    </div>
                                </div>
                                @php $b++; @endphp
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<div class="modal fade" id="replaceUrlModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="replaceUrlForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Replace URLs in Blogs</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Old URL</label>
                        <input type="text" name="old_url" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>New URL</label>
                        <input type="text" name="new_url" class="form-control" required>
                    </div>

                    <button type="button" id="previewBtn" class="btn btn-info mb-3">
                        Preview Changes
                    </button>

                    <div id="previewResult"></div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">
                        Confirm Replace
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('script')

    <!-- Start datatable js -->
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {

            $(document).on('click','#bulk_delete_btn',function (e) {
                e.preventDefault();

                var bulkOption = $('#bulk_option').val();
                var allCheckbox =  $('.bulk-checkbox:checked');
                var allIds = [];
                allCheckbox.each(function(index,value){
                    allIds.push($(this).val());
                });
                if(allIds != '' && bulkOption == 'delete'){
                    $(this).text('{{__('Deleting...')}}');
                    $.ajax({
                        'type' : "POST",
                        'url' : "{{route('admin.case-study.bulk.action')}}",
                        'data' : {
                            _token: "{{csrf_token()}}",
                            ids: allIds
                        },
                        success:function (data) {
                            location.reload();
                        }
                    });
                }

            });

            $('.all-checkbox').on('change',function (e) {
                e.preventDefault();
                var value = $('.all-checkbox').is(':checked');
                var allChek = $(this).parent().parent().parent().parent().parent().find('.bulk-checkbox');
                //have write code here fr
                if( value == true){
                    allChek.prop('checked',true);
                }else{
                    allChek.prop('checked',false);
                }
            });

            $('.table-wrap > table').DataTable( {
                "order": [[ 1, "desc" ]],
                'columnDefs' : [{
                    'targets' : 'no-sort',
                    'orderable' : false
                }]
            } );
        } );
    </script>
    <script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"
    }
});

let table = $('#casestudyTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('admin.case-study.datatable') }}",
        type: "POST",
        data: function (d) {
            d.keyword = $('#search').val();
        }
    },
    columns: [
         { data: 'checkbox', searchable: false },
        { data: 'id', searchable: false },
        { data: 'title', searchable: true },  
        { data: 'status', searchable: false },
        
       {
        data: 'image',
        searchable: false,
        render: function (data, type, row) {
            if (data && data.path) {
                return `<img src="../assets/uploads/media-uploader/${data.path}" width="100" height="150">`;
            }
            return 'No Image';
        }
    },
        { data: 'category', searchable: false },
        { data: 'created_at', searchable: false },
        { data: 'action', searchable: false }
    ]
});

// 🔍 Live filters
let delayTimer;

$('#search').keyup(function () {
    clearTimeout(delayTimer);
    delayTimer = setTimeout(function () {
        table.draw();
    }, 300);
});

// $('#statusFilter, #categoryFilter').change(function () {
//     table.draw();
// });

// ✅ Suggestion click integration
$(document).on('click', '.suggestion-item', function () {
    let keyword = $(this).contents().get(0).nodeValue.trim();

    $('#search').val(keyword);
    $('#suggestions').hide();

    table.draw();
});
</script>
<script>
$('#previewBtn').on('click', function() {

    let formData = $('#replaceUrlForm').serialize();

    $('#previewResult').html('Loading preview...');

    $.ajax({
        url: "{{ route('admin.case-study.replace.preview') }}",
        method: "POST",
        data: formData,
        success: function(res) {

            let html = `<p><strong>${res.count} b2b will be affected</strong></p>`;

            html += `<table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Preview</th>
                        </tr>`;

            res.data.forEach(row => {
                html += `<tr>
                            <td>${row.id}</td>
                            <td>${row.title}</td>
                            <td>${row.preview}</td>
                        </tr>`;
            });

            html += `</table>`;

            $('#previewResult').html(html);
        }
    });
});
</script>

<script>
$('#replaceUrlForm').on('submit', function(e) {
    e.preventDefault();

    let formData = $(this).serialize();

    if (!confirm('Are you sure? This will update ALL b2b!')) {
        return;
    }

    $.ajax({
        url: "{{ route('admin.case-study.replace.url') }}",
        method: "POST",
        data: formData,
        success: function(res) {
            alert(res.message);
            $('#replaceUrlModal').modal('hide');
            location.reload();
        },
        error: function() {
            alert('Something went wrong!');
        }
    });
});

$("#startScanBtn").click(function () {

    // Show progress UI
    $("#scanBox").show();
    $("#progressBar").css("width", "0%").text("0%");
    $("#scanStatus").text("Starting scan...");

    // Start scan
    $.get("{{ route('admin.scan.full') }}", function (res) {

        // Start checking progress
        checkProgress();
    });
});


function checkProgress() {

    $.get("{{ route('admin.scan.progress') }}", function (res) {

        let percent = res.percent;

        $("#progressBar")
            .css("width", percent + "%")
            .text(percent + "%");

        $("#scanStatus").text(
            "Processed: " + res.processed + " / " + res.total
        );

        if (res.running == 1) {

            setTimeout(checkProgress, 2000);

        } else {

            $("#progressBar")
                .removeClass("progress-bar-animated")
                .addClass("bg-success")
                .text("Completed ✅");

            $("#scanStatus").text("Scan completed. Redirecting...");

            // 🔥 AUTO REDIRECT
            setTimeout(function () {
                window.location.href = "{{ route('admin.scan.results') }}";
            }, 2000);
        }
    });
}
</script>
@endsection
