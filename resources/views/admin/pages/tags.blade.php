@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/velocity/velocity.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/velocity/velocity.ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/notifications/bootbox.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
    <script type="text/javascript">
        $(function(){
            // Table setup
            // ------------------------------

            $('.table-tag').DataTable({
                autoWidth: false,
                dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
                language: {
                    search: '<span>Filter:</span> _INPUT_',
                    lengthMenu: '<span>Show:</span> _MENU_',
                    paginate: {'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;'}
                },
                columnDefs: [{
                    orderable: true,
                    width: '50px',
                    visible: true,
                    targets: [0],
                }, {
                    orderable: false,
                    targets: [2],
                }],
            });


            // External table additions
            // ------------------------------

            // Add placeholder to the datatable filter option
            $('.dataTables_filter input[type=search]').attr('placeholder', 'Type to filter...');

            // Enable Select2 select for the length option
            $('.dataTables_length select').select2({
                minimumResultsForSearch: Infinity,
                width: 'auto'
            });

            /**
             * Add Tags Dlg
             */
            $('#btn_add_tag').on('click', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "GET",
                    url: './ajax/tags/next_id',
                    dataType: 'json',
                    success: function (data) {
                        $('.form-group input').val('');
                        $('#tag_id').val(data.tag_id);
                        $('.form-group input').prop('readonly', false);
                        $('#btn_tag_submit').removeClass('btn-danger');
                        $('#btn_tag_submit').addClass('btn-success');
                        $('#btn_tag_submit').html('Add Tag');
                        $('#btn_tag_submit').data('value', 'add');
                        $('#modal_tag').modal('show');
                    },
                });
            });

            /**
             * Edit Tags Dlg
             */
            $('table').on('click', '.btn_edit_tag', function() {
                var tag_id = $(this).data('value');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "GET",
                    url: './ajax/tags/get',
                    data: {"tag_id": tag_id},
                    dataType: 'json',
                    success: function (data) {
                        $('#tag_id').val(data.tag_id);
                        $('#name_en').val(data.tag_name);
                        // $('#name_de').val(data.name_de);
                        // $('#name_pl').val(data.name_pl);
                        // $('#name_es').val(data.name_es);
                        // $('#name_fr').val(data.name_fr);
                        $('.form-group input').prop('readonly', false);
                        $('#btn_tag_submit').removeClass('btn-danger');
                        $('#btn_tag_submit').addClass('btn-success');
                        $('#btn_tag_submit').html('Update');
                        $('#btn_tag_submit').data('value', 'edit');
                        $('#modal_tag').modal('show');
                    }
                });
            });

            /**
             * Delete Tags Dlg
             */
            $('table').on('click', '.btn_delete_tag', function() {
                var tag_id = $(this).data('value');
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "GET",
                        url: './ajax/tags/get',
                        data: {"tag_id": tag_id},
                        dataType: 'json',
                        success: function (data) {
                            $('#tag_id').val(data.tag_id);
                            $('#name_en').val(data.tag_name);
                            // $('#name_de').val(data.name_de);
                            // $('#name_pl').val(data.name_pl);
                            // $('#name_es').val(data.name_es);
                            // $('#name_fr').val(data.name_fr);
                            $('.form-group input').prop('readonly', true);
                            $('#btn_tag_submit').removeClass('btn-success');
                            $('#btn_tag_submit').addClass('btn-danger');
                            $('#btn_tag_submit').html('Delete');
                            $('#modal_title').html('Do you really want to delete this tag?');
                            $('#btn_tag_submit').data('value', 'delete');
                            $('#modal_tag').modal('show');
                        }
                    });
                
            });

            /**
             * Submit with Tag Dialog
             */
            $("#btn_tag_submit").on("click", function(e) {
                e.preventDefault();
                var slug = $('#modal_tag form').attr('action') + "/" + $('#btn_tag_submit').data('value');
                $('#modal_tag form').attr('action', slug).submit();
            });
        });
    </script>
@endsection


@section("content")

<meta name="_token" content="{!! csrf_token() !!}" />

<!-- Main content -->
<div class="content-wrapper">

    <!-- Page header -->
    <div class="page-header page-header-default">
        <div class="page-header-content">
            <div class="page-title">
                <h3><span class="text-semibold"> Tags</span></h3>
            </div>
        </div>

        <div class="breadcrumb-line">
            <ul class="breadcrumb">
                <li><a href="{{ URL::to('/admin/transactions/index')}}"><i class="icon-home2 position-left"></i> Tags</a></li>
            </ul>

            <ul class="breadcrumb-elements">
                <li><a href="#" data-toggle="modal" data-target="#modal_tag" id="btn_add_tag"><i class="icon-add position-left"></i> Add Tag</a></li>
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <!-- Error Message -->
        @if (count($errors) > 0)
        <div class="alert alert-danger no-border">
            <ul>
                <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                @foreach ($errors->all() as $error)
                    <li>
                        <span class="text-semibold">{{ $error }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Success Message -->
        @if(Session::has('flash_message'))
            <div class="alert alert-success no-border">
                <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                <span class="text-semibold">{{ Session::get('flash_message') }}</span>
            </div>
        @endif

        <div class="panel panel-flat">
            <table class="table table-tag table-scrollable table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tag Name (English)</th>
                   <!--  <th>Tag Name (German)</th>
                    <th>Tag Name (Polish)</th>
                    <th>Tag Name (Spanish)</th>
                    <th>Tag Name (French)</th>  -->
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tags as $i => $tag)
                    <tr>
                        <td>{{$tag->tag_id}}</td>
                        <td>{{$tag->tag_name}}</td>
                       <!--  <td>{{$tag->tag_name}}</td>
                        <td>{{$tag->tag_name}}</td>
                        <td>{{$tag->tag_name}}</td>
                        <td>{{$tag->tag_name}}</td> -->
                        <td class="text-center">
                            <ul class="icons-list">
                                <li><a href="#" class="btn_edit_tag" data-value="{{ $tag->tag_id }}" data-toggle="modal" data-target="#modal_tag"><i class="icon-pencil7"></i></a></li>
                                <li><a href="#" class="btn_delete_tag" data-value="{{ $tag->tag_id }}" data-toggle="modal" data-target="#modal_tag"><i class="icon-trash"></i></a></li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- /content area -->

    <!-- Tag Dialog modal -->
    <div id="modal_tag" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 id="modal_title" class="modal-title">Tag Information</h5>
                </div>

                <form action="{{url('admin/tags/')}}" method="post" enctype="multipart/form-data">
                    {!! csrf_field() !!}

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tag ID</label>
                            <input type="number" id="tag_id" name="tag_id" class="form-control" step="1" readonly="readonly" required>
                        </div>

                        <div class="form-group">
                            <label>Tag Name - English</label>
                            <input type="text" id="name_en" name="tag_name" placeholder="" class="form-control" required>
                        </div>

                        <!-- <div class="form-group">
                            <label>Tag Name - German</label>
                            <input type="text" id="name_de" name="name_de" placeholder="" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Tag Name - Polish</label>
                            <input type="text" id="name_pl" name="name_pl" placeholder="" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Tag Name - Spanish</label>
                            <input type="text" id="name_es" name="name_es" placeholder="" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Tag Name - French</label>
                            <input type="text" id="name_fr" name="name_fr" placeholder="" class="form-control" required>
                        </div>
 -->
                        <div class="form-group mt-20 pt-10">
                            <button type="submit" id="btn_tag_submit" class="btn btn-success form-control">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Tag Dialog modal -->
</div>
<!-- /main content -->
@endsection



