@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>


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
                    <li><a href="{{ URL::to('/admin/tricks')}}"><i class="icon-home2 position-left"></i>Tricks</a></li>
                </ul>

                <ul class="breadcrumb-elements">
                    <li><a href="{{ URL::to('/admin/tricks/add')}}" ><i class="icon-add position-left"></i> Add Tricks</a></li>
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
                <table class="table table-category table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Trick Name</th>
                        <th>Category</th>
                        <th>Trick Tags</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($tricks as $i => $trick)
                        <tr>
                            <td>{{$i + 1}}</td>
                            <td>{{$trick->trick_title}}</td>
                            <td>{{$trick->category_title}}</td>
                            <td>{{$trick->trick_tags}}</td>

                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>

                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="{{URL::to('admin/tricks/' . $trick->trick_id . '/edit')}}">Edit</a></li>
                                            <li><a href="{{URL::to('admin/tricks/' . $trick->trick_id . '/delete')}}" onclick="return confirm('Are you sure that delete category?')">Delete</a></li>
                                        </ul>
                                    </li>
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



