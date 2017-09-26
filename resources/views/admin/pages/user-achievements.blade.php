@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/switch.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/velocity/velocity.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/velocity/velocity.ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/buttons/spin.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/buttons/ladda.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/notifications/bootbox.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_select2.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_inputs.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/components_modals.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/category.js') }}"></script>
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
                    <li><a href="{{ URL::to('/admin/users/' . $user_id . '/edit') }}">{{ $user_id }}</a></li>
                    <li><a href="{{ URL::to('/admin/users/' . $user_id . '/achievements') }}">Achievements</a></li>
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
                                <th>Trick ID</th>
                                <th>Achievement</th>
                                <th>Score</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($achievements['data'] as $achievement)
                            <tr>
                                <td>{{$achievement['trick']->trick_id}}</td>
                                <td>
                                    <a href="#" data-popup="lightbox">
                                    @if ($achievement['average'] >= 9) 
                                    <img src="{{URL::asset('admin_assets/images/achievement_gold.png')}}" alt="" class="img-rounded img-preview">
                        			@elseif ($achievement['average'] >= 7) 
                        			<img src="{{URL::asset('admin_assets/images/achievement_silver.png')}}" alt="" class="img-rounded img-preview">
                        			@elseif ($achievement['average'] >= 5) 
                        			<img src="{{URL::asset('admin_assets/images/achievement_bronze.png')}}" alt="" class="img-rounded img-preview">
                        			@else 
                        			<img src="{{URL::asset('admin_assets/images/achievement_grayed.png')}}" alt="" class="img-rounded img-preview">
                        			@endif
                                        
                                    </a>
                                </td>
                                <td>{{$achievement['average']}}</td>
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



