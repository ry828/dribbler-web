@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>

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
                <li><a href="{{ URL::to('/admin/tags/index')}}"><i class="icon-home2 position-left"></i> Tags</a></li>
            </ul>

            <ul class="breadcrumb-elements">
                <li><a href="{{ URL::to('/admin/tags/add')}}" ><i class="icon-add position-left"></i> Add Tag</a></li>
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
                    <th>Tag Name</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tags as $i => $tag)
                    <tr>
                        <td>{{$tag->tag_id}}</td>
                        <td>{{$tag->tag_name}}</td>
                        <td class="text-center">
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="{{URL::to('admin/tags/' . $tag->tag_id . '/edit')}}">Edit</a></li>
                                        <li><a href="{{URL::to('admin/tags/' . $tag->tag_id . '/delete')}}" onclick="return confirm('Are you sure that delete tag?')">Delete</a></li>
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

</div>
<!-- /main content -->
@endsection



