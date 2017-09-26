@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('.data-table').DataTable({
                autoWidth: false,
                processing: true,
                serverSide: true,
                dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
                ajax: '{{ url('admin/tricks') }}',
                columnDefs: [{
                    width: '80px',
                    targets: 0
                },{
                    render: function ( data, type, row ) {
                        return '<a class="text-primary" href="./tricks/' + row.trick_id + '">' + row.trick_title + '</a>' ;
                    },
                    targets: 1,
                }],
                columns: [
                    {data: 'trick_id', name: 'trick_id'},
                    {data: 'trick_title', name: 'trick_title'},
                    {data: 'category_title', name: 'category_title'},
                    {data: 'trick_tags', name: 'trick_tags'},
                ],
                language: {
                    search: '<span>Filter:</span> _INPUT_',
                    lengthMenu: '<span>Show:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
                },
            });

            // Add placeholder to the datatable filter option
            $('.dataTables_filter input[type=search]').attr('placeholder','Type to filter...');

            // Enable Select2 select for the length option
            $('.dataTables_length select').select2({
                minimumResultsForSearch: Infinity,
                width: 'auto'
            });
        });
    </script>
@endsection


@section("content")
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header page-header-default">
            <div class="page-header-content">
                <div class="page-title">
                    <h3><span class="text-semibold">Tricks Overview</span></h3>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="{{ URL::to('/admin/tricks')}}"><i class="icon-home2 position-left"></i>Tricks</a></li>
                </ul>

                <ul class="breadcrumb-elements">
                    <li><a href="{{url('admin/tricks/add')}}"><i class="icon-add position-left"></i>Add Trick</a></li>
                </ul>
            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">
            <div class="panel panel-flat">
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

                <table class="table data-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Trick Tags</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!-- /content area -->
    </div>
    <!-- /main content -->
@endsection



