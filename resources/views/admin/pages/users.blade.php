@extends("admin/admin_app")


@section("js")


    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/users.js') }}"></script>

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
                    <li><a href="{{ URL::to('/admin/users/') }}"><i class="icon-home2 position-left"></i> Users</a></li>
                </ul>

                <ul class="breadcrumb-elements">
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
                            <button type="button" class="close" data-dismiss="alert"><span>Ã—</span><span class="sr-only">Close</span></button>
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
                        <button type="button" class="close" data-dismiss="alert"><span>Ã—</span><span class="sr-only">Close</span></button>
                        <span class="text-semibold">{{ Session::get('flash_message') }}</span>
                    </div>
                @endif
            
                <table class="table datatable-show-all datatable-selection-single datatable-selection-multiple table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Birthday</th>
                        <th>Subscribed</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $i => $user)
                    <tr>
                        <td>{{$i + 1}}</td>
                        <td>{{$user->first_name .' '. $user->last_name}}</td>
                        <td>{{$user->email}}</td>
                        <td>
                            @if ($user->gender == 0)
                                    Male
                            @else
                                    Female
                            @endif
                        </td>

                        <td>{{$user->birthday}}</td>
                        @if ($user->subscribe == 0)
                            <td>Not yet</td>
                        @else
                            <td><span class="label label-success">Subscribed</span></td>
                        @endif
                        @if ($user->status == 'active')
                            <td><span class="label label-success">Active</span></td>
                        @else
                            <td><span class="label label-default">Inactive</span></td>
                        @endif

                        <td class="text-center">
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li><a href="{{URL::to('admin/users/' . $user->id . '/edit')}}">Edit</a></li>
                                        <li><a href="{{URL::to('admin/users/' . $user->id . '/active')}}">Active</a></li>
                                        <li><a href="{{URL::to('admin/users/' . $user->id . '/inactive')}}" onclick="return confirm('Are you sure that inactive user?')">Inactive</a></li>
                                        <li><a href="{{URL::to('admin/users/' . $user->id . '/delete')}}" onclick="return confirm('Are you sure that delete user?')">Delete</a></li>
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


