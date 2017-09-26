@extends("admin/admin_app")


@section("js")

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
@endsection


@section("content")
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header page-header-default">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-left52 position-left"></i><span class="text-semibold">Users</span></h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="{{ URL::to('/admin/users') }}"><i class="icon-home2 position-left"></i>Users</a></li>
                    <li><a href="{{ URL::to('/admin/users/' . $user->id . '/edit') }}">{{ $user->id }}</a></li>
                    <li><a href="{{ URL::to('/admin/users/' . $user->id . '/videos') }}">Videos</a></li>
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

            <div class="row">
                <div class="panel panel-flat">
                    <div class="panel-body">
                        <table class="table datatable-show-all datatable-selection-single datatable-selection-multiple table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Video ID</th>
                                <th>Video</th>
                                <th>Likes</th>
                                <th>Views</th>
                                <th>Comments</th>
                                <th class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($videos as $i => $video)
                            <tr>
                                <td>{{$video->video_id}}</td>
                                <td>
                                    <a href="#" data-popup="lightbox">
                                        <img src="{{ $video->thumbnail }}" alt="" class="img-rounded img-preview">
                                    </a>
                                </td>
                                <td>{{$video->likes}}</td>
                                <td>{{$video->views}}</td>
                                <td>{{$video->comments}}</td>
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>

                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{URL::to('admin/users/' . $video->video_id.'/comments')}}">View Comments</a></li>
                                                <li><a href="{{URL::to('admin/users/' . $video->video_id.'/edit_video')}}">Edit</a></li>
                                                <li><a href="{{URL::to('admin/users/' . $video->video_id.'/delete_video')}}" onclick="return confirm('Are you sure to delete video?')">Delete</a></li>
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
            </div>
        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->
@endsection



