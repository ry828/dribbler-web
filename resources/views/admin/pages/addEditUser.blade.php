@extends("admin/admin_app")


@section("js")
    <!-- <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script> -->
    <!-- <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script> -->
    <!-- <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script> -->
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/switch.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_checkboxes_radios.js') }}"></script> -->
    <!-- <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/velocity/velocity.min.js') }}"></script> -->
<!--     <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/velocity/velocity.ui.min.js') }}"></script> 
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/buttons/spin.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/buttons/ladda.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/notifications/bootbox.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
 -->
<!--     <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_select2.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_inputs.js') }}"></script>
    
 -->
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
    <script type="text/javascript">
        var user_id = "{{ $user->id }}";
        $(function() {
            
            $("input[type=checkbox]#ch_facebook").change(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                var status = 0;
                console.dir(this.checked);
                if (this.checked == true) {
                    status = 1;
                } 
                console.dir(status);
                $.ajax({
                    type: "GET",
                    url: './ajax/update_connection',
                    data: {'type':'facebook',
                            'status': status,
                            'user_id': user_id},
                    dataType: 'json',
                    success: function (data) {
                       console.log(data);
                    },

                });
            });
            $("input[type=checkbox]#ch_google").on("change", function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                var status = 0;
                if (this.checked == true) {
                    status = 1;
                } 
                console.dir(status);
                $.ajax({
                    type: "GET",
                    url: './ajax/update_connection',
                    data: {'type':'google',
                            'status': status,
                            'user_id': user_id},
                    dataType: 'json',
                    success: function (data) {
                       console.log(data);
                    },

                });
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
                    <h4><i class="icon-arrow-left52 position-left"></i><span class="text-semibold">Users</span></h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="{{ URL::to('/admin/users')}}"><i class="icon-home2 position-left"></i>Users</a></li>
                </ul>

                <ul class="breadcrumb-elements">
                    <li><a href="{{ url('admin/users/' . $user->id . '/achievements') }}"><i class="icon-medal-first position-left"></i>Achievement</a></li>
                    <li><a href="{{ url('admin/users/' . $user->id . '/payments') }}"><i class="icon-coin-dollar position-left"></i>Payment History</a></li>
                    <li><a href="{{ url('admin/users/' . $user->id . '/videos') }}"><i class="icon-file-play position-left"></i>Videos</a></li>
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
                    <div class="col-md-8">
                        <div class="panel panel-flat">
                            <div class="panel-body">
                                <form class="form-horizontal" action="{{url('/admin/users/update')}}" enctype="multipart/form-data" method="post">
                                    {{csrf_field()}}

                                    @if(isset($user))
                                        <input type="hidden" name="id" value="{{$user->id}}">
                                    @endif
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">First Name</label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="first_name" placeholder="first name" value="{{ isset($user) ? $user->first_name : old('first_name')}}" required>
                                                @if ($errors->has('first_name'))
                                                    <span class="help-block">{{ $errors->first('first_name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Last Name</label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="last_name" placeholder="last name" value="{{ isset($user) ? $user->last_name : old('last_name')}}" required>
                                                @if ($errors->has('last_name'))
                                                    <span class="help-block">{{ $errors->first('last_name') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Email</label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="email" placeholder="email" value="{{ isset($user) ? $user->email : old('email')}}" required>
                                                @if ($errors->has('email'))
                                                    <span class="help-block">{{ $errors->first('email') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Birthday</label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" name="birthday" placeholder="yyyy-mm-dd" value="{{ isset($user) ? $user->birthday : old('birthday')}}" required>
                                                @if ($errors->has('birthday'))
                                                    <span class="help-block">{{ $errors->first('birthday') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="form-group">
                                           <label class="control-label col-lg-2">Gender</label>
                                            <div class="col-lg-10">
                                                <select name="gender" class="select_nric form-control">
                                                    <option @if (isset($user) && $user->gender == '0') selected="selected" @endif  value="0">Male</option>
                                                    <option @if (isset($user) && $user->gender == '1') selected="selected" @endif value="1">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="form-group">
                                           <label class="control-label col-lg-2">Subscription</label>
                                            <div class="col-lg-10">
                                                <select name="subscribe" class="select_nric form-control">
                                                    <option @if (isset($user) && $user->subscribe == '0') selected="selected" @endif  value="0">Beginner</option>
                                                    <option @if (isset($user) && $user->subscribe == '1') selected="selected" @endif value="1">Advanced</option>
                                                    <option @if (isset($user) && $user->subscribe == '2') selected="selected" @endif value="2">Professional</option>
                                                    <option @if (isset($user) && $user->subscribe == '3') selected="selected" @endif value="3">All subscribed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary"> Save<i class="icon-arrow-right14 position-right"></i></button>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-flat">
                            <div class="panel-body">
                                <form class="form-horizontal">
                                    
                                    <fieldset>
                                        <div class="form-group no-margin">
                                            <div class="checkbox checkbox-right checkbox-switchery">
                                                <label class="display-block">
                                                    <input type="checkbox" class="switchery" id="ch_facebook" @if (isset($user) && $user->fb_enable == '1') checked="checked"  @endif>
                                                    Facebook Connection
                                                </label>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="form-group no-margin">
                                            <div class="checkbox checkbox-right checkbox-switchery">
                                                <label class="display-block">
                                                    <input type="checkbox" class="switchery" id="ch_google" @if (isset($user) && $user->google_enable == '1') checked="checked"  @endif>
                                                    Google Connection
                                                </label>
                                            </div>
                                        </div>
                                    </fieldset>
                                    
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->
@endsection



