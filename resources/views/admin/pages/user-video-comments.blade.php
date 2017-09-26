@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/transaction.js') }}"></script>
@endsection


@section("content")
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header page-header-default">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-left52 position-left"></i><span class="text-semibold">Comments</span></h4>
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

            <div class="panel panel-flat">
                <div class="panel-body">
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

                    <table class="table datatable-show-all datatable-selection-single datatable-selection-multiple table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>User Name</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($comments as $i => $comment)
                            <tr>
                                <td>{{$i + 1}}</td>
                                <td>{{$comment->name}}</td>
                                <td>{{$comment->message}}</td>
                                <td>{{$comment->created_at}}</td>
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu9"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="{{URL::to('admin/users/' . $video_id .'/'.$comment->comment_id.'/edit_comment')}}">Edit</a></li>
                                                <li><a href="{{URL::to('admin/users/' . $comment->comment_id . '/delete_comment')}}" onclick="return confirm('Are you sure to delete comment?')">Delete</a></li>
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
        <!-- /content area -->
    </div>
    <!-- /main content -->
@endsection



