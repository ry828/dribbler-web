@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/switch.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>

    <script type="text/javascript">
        $(function(){
            // Basic example
            $('.file-input').fileinput({
                browseLabel: 'Browse',
                browseIcon: '<i class="icon-file-plus"></i>',
                uploadIcon: '<i class="icon-file-upload2"></i>',
                removeIcon: '<i class="icon-cross3"></i>',
                showUpload: false,
                layoutTemplates: {
                    icon: '<i class="icon-file-check"></i>'
                },
                initialCaption: "No file selected"
            });
            $('.file-input').change(function () {
               $('.photo').attr('hidden', true);
            });
        });
    </script>
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
                                            <img src="{{$user->photo}}" class="photo" style="width:150px;height:150px;">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-lg-2">Photo</label>
                                            <div class="col-lg-10">

                                                <input type="file"  class="file-input" name="photo" accept=".png, .jpg" data-allowed-file-extensions='["png", "jpg"]' data-show-caption="true" @if (!isset($user)) required @endif>
                                            </div>
                                        </div>
                                    </fieldset>

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
                                            <label class="control-label col-lg-2">Role</label>
                                            <div class="col-lg-10">
                                                <select name="role" class="select_nric form-control">
                                                    <option @if (isset($user) && $user->role == 'admin') selected="selected" @endif  value="admin">Admin</option>
                                                    <option @if (isset($user) && $user->gender == 'guest') selected="selected" @endif value="guest">Guest</option>
                                                </select>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="control-label col-lg-2">Notification Enable</label>
                                            <div class="col-lg-10">
                                                <select name="push_enable" class="select_nric form-control">
                                                    <option @if (isset($user) && $user->push_enable == '1') selected="selected" @endif  value="0">Enable</option>
                                                    <option @if (isset($user) && $user->push_enable == '0') selected="selected" @endif value="1">Disable</option>
                                                </select>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="control-label col-lg-2">High Quality Video</label>
                                            <div class="col-lg-10">
                                                <select name="high_video_enable" class="select_nric form-control">
                                                    <option @if (isset($user) && $user->high_video_enable == '1') selected="selected" @endif  value="0">Enable</option>
                                                    <option @if (isset($user) && $user->high_video_enable == '0') selected="selected" @endif value="1">Disable</option>
                                                </select>
                                            </div>
                                        </div>
                                    </fieldset>
                                    @if (isset($user))
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Following Count</label>
                                                <div class="col-md-10">
                                                    <input type="number" class="form-control" name="following_count" placeholder="0" value="{{ isset($user) ? $user->following_count : old('0')}}" readonly="readonly" required>
                                                    @if ($errors->has('following_count'))
                                                        <span class="help-block">{{ $errors->first('following_count') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Follower Count</label>
                                                <div class="col-md-10">
                                                    <input type="number" class="form-control" name="follower_count" placeholder="0" value="{{ isset($user) ? $user->follower_count : old('0')}}" readonly="readonly" required>
                                                    @if ($errors->has('follower_count'))
                                                        <span class="help-block">{{ $errors->first('follower_count') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Video Count</label>
                                                <div class="col-md-10">
                                                    <input type="number" class="form-control" name="video_count" placeholder="0" value="{{ isset($user) ? $user->video_count : old('0')}}" readonly="readonly" required>
                                                    @if ($errors->has('video_count'))
                                                        <span class="help-block">{{ $errors->first('video_count') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Overal Ranking</label>
                                                <div class="col-md-10">
                                                    <input type="number" class="form-control" name="overall_ranking" placeholder="0" value="{{ isset($user) ? $user->overall_ranking : old('0')}}" readonly="readonly" required>
                                                    @if ($errors->has('overall_ranking'))
                                                        <span class="help-block">{{ $errors->first('overall_ranking') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Dribble Score</label>
                                                <div class="col-md-10">
                                                    <input type="number" class="form-control" name="dribble_score" placeholder="0" value="{{ isset($user) ? $user->dribble_score : old('0')}}" readonly="readonly" required>
                                                    @if ($errors->has('dribble_score'))
                                                        <span class="help-block">{{ $errors->first('dribble_score') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Dribble Medal</label>
                                                <div class="col-md-10">
                                                    <input type="number" class="form-control" name="dribble_medal" placeholder="0" value="{{ isset($user) ? $user->dribble_medal : old('0')}}" readonly="readonly" required>
                                                    @if ($errors->has('dribble_medal'))
                                                        <span class="help-block">{{ $errors->first('dribble_medal') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Trick Completion Count</label>
                                                <div class="col-md-10">
                                                    <input type="number" class="form-control" name="trick_completion_count" placeholder="0" value="{{ isset($user) ? $user->trick_completion_count : old('0')}}" readonly="readonly" required>
                                                    @if ($errors->has('trick_completion_count'))
                                                        <span class="help-block">{{ $errors->first('trick_completion_count') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Subscribed</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control" name="subscribe" placeholder="0" value="@if (isset($user)) @if ($user->subscribe == 0) Not yet @else Subscribed @endif @endif" readonly="readonly" required>
                                                    @if ($errors->has('created_at'))
                                                        <span class="help-block">{{ $errors->first('created_at') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form-group">
                                                <label class="control-label col-md-2">Registered Date</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control" name="created_at" placeholder="0" value="{{ isset($user) ? $user->created_at : old('0')}}" readonly="readonly" required>
                                                    @if ($errors->has('created_at'))
                                                        <span class="help-block">{{ $errors->first('created_at') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </fieldset>
                                    @endif



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



