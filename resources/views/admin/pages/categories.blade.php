@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/notifications/bootbox.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_select2.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_inputs.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/components_modals.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/category.js') }}"></script>
    <script type="text/javascript">
        $(function(){
            // Table setup
            // ------------------------------

            $('.table-category').DataTable({
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
                    targets: [3],
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
             * Add Category Dlg
             */
            $('#btn_add_category').on('click', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "GET",
                    url: './ajax/categories/next_id',
                    dataType: 'json',
                    success: function (data) {
                        $('.form-group input').val('');
                        $('#category_id').val(data.category_id);
                        $('.form-group input').prop('readonly', false);
                        $('#btn_category_submit').removeClass('btn-danger');
                        $('#btn_category_submit').addClass('btn-success');
                        $('#btn_category_submit').html('Add category');
                        $('#btn_category_submit').data('value', 'add');
                        $('#modal_category').modal('show');
                    },
                });
            });
            /**
             * Edit category Dlg
             */
            $('table').on('click', '.btn_edit_category', function() {
                var category_id = $(this).data('value');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "GET",
                    url: './ajax/categories/get',
                    data: {"category_id": category_id},
                    dataType: 'json',
                    success: function (data) {
                        $('#category_id').val(data.category_id);
                        $('#category_title').val(data.category_title);
                        $('#price').val(data.price);
                        $('.form-group input').prop('readonly', false);
                        $('#btn_category_submit').removeClass('btn-danger');
                        $('#btn_category_submit').addClass('btn-success');
                        $('#btn_category_submit').html('Update');
                        $('#btn_category_submit').data('value', 'edit');
                        $('#modal_category').modal('show');
                    }
                });
            });

            /**
             * Delete category Dlg
             */
            $('table').on('click', '.btn_delete_category', function() {
                var category_id = $(this).data('value');
                $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "GET",
                        url: './ajax/categories/get',
                        data: {"category_id": category_id},
                        dataType: 'json',
                        success: function (data) {
                            $('#category_id').val(data.category_id);
                            $('#category_title').val(data.category_title);
                            $('#price').val(data.price);
                            $('.form-group input').prop('readonly', true);
                            $('#btn_category_submit').removeClass('btn-success');
                            $('#btn_category_submit').addClass('btn-danger');
                            $('#btn_category_submit').html('Delete');
                            $('#modal_title').html('Do you really want to delete this category?');
                            $('#btn_category_submit').data('value', 'delete');
                            $('#modal_category').modal('show');
                        }
                    });
                
                });

                /**
                 * Submit with category Dialog
                 */
                $("#btn_category_submit").on("click", function(e) {
                    e.preventDefault();
                    var slug = $('#modal_category form').attr('action') + "/" + $('#btn_category_submit').data('value');
                    $('#modal_category form').attr('action', slug).submit();
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
                <h3><span class="text-semibold"> Categories</span></h3>
            </div>
        </div>

        <div class="breadcrumb-line">
            <ul class="breadcrumb">
                <li><a href="{{ URL::to('/admin/transactions/index')}}"><i class="icon-home2 position-left"></i>Categories</a></li>
            </ul>

            <ul class="breadcrumb-elements">
                <li><a href="#" data-toggle="modal" data-target="#modal_category" id="btn_add_category"><i class="icon-add position-left"></i> Add Category</a></li>
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
            <table class="table table-category table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name - English</th>
                   <!--  <th>Name - German</th>
                    <th>Name - Polish</th>
                    <th>Name - Spanish</th>
                    <th>Name - French</th> -->
                    <th>Lock</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>

                @foreach($categories as $i => $category)
                    <tr>
                        <td>{{$category->category_id}}</td>
                        <td>{{$category->category_title}}</td>
                       <!--  <td>{{$category->category_title}}</td>
                        <td>{{$category->category_title}}</td>
                        <td>{{$category->category_title}}</td>
                        <td>{{$category->category_title}}</td> -->
                        <td>{{$category->lock}}</td>

                        <td class="text-center">
                            <ul class="icons-list">
                                <li><a href="#" class="btn_edit_category" data-value="{{$category->category_id}}" data-toggle="modal" data-target="#modal_category"><i class="icon-pencil7"></i></a></li>
                                <!-- <li><a href="#" class="btn_delete_category" data-value="{{$category->category_id}}" data-toggle="modal" data-target="#modal_category"><i class="icon-trash"></i></a></li> -->
                            </ul>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <!-- /content area -->


    <!-- category Dialog modal -->
    <div id="modal_category" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 id="modal_title" class="modal-title">Category Information</h5>
                </div>

                <form action="{{url('admin/categories')}}" enctype="multipart/form-data" method="post">

                    {!! csrf_field() !!}

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category ID</label>
                            <input type="number" id="category_id" name="category_id" class="form-control" step="1">
                        </div>

                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" id="category_title" name="category_title" placeholder="" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Free or Premium</label>
                            <select class="select form-control" name="lock">
                                <option value="0">Free</option>
                                <option value="1">Premium</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Category Price</label>
                            <input type="number" id="price" placeholder="" class="form-control" name="price" step="0.01">
                        </div>

                        <div class="form-group">
                            <label>Category Photo</label>
                            <input type="file" class="file-styled" name="thumbnail">
                        </div>

                        <div class="form-group">
                            <button type="submit" id="btn_category_submit" class="btn btn-success form-control">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /category Dialog modal -->

</div>
<!-- /main content -->
@endsection



