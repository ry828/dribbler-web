@extends("admin/admin_app")


@section("js")
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/core/app.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_select2.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/form_inputs.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/components_modals.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('admin_assets/js/pages/category.js') }}"></script>
    
    <script type="text/javascript">
        $(function() {
            var unlock_rules =  JSON.parse('{{ $rules }}'.replace(/&quot;/g, '"'));
            console.dir(unlock_rules);
            set_value(0);
            $('select').on('change',function() {
                var index = this.selectedIndex;
                console.dir(index);
                set_value(index);
            });

            function set_value(index) {
                var rule = unlock_rules[index];
                $('#facebook_connect').val(rule['facebook_connect']);
                $('#google_connect').val(rule['google_connect']);
                $('#upload_video_count_per_trick_amature').val(rule['upload_video_count_per_trick_amature']);
                $('#view_count_per_video_amature').val(rule['view_count_per_video_amature']);
                $('#upload_video_count_per_trick_advanced').val(rule['upload_video_count_per_trick_advanced']);
                $('#view_count_per_video_advanced').val(rule['view_count_per_video_advanced']);
                $('#try_count_amature').val(rule['try_count_amature']);
                $('#dribble_average_amature').val(rule['dribble_average_amature']);
                $('#try_count_advanced').val(rule['try_count_advanced']);
                $('#dribble_average_advanced').val(rule['dribble_average_advanced']);
                $('#dribble_score').val(rule['dribble_score']);
                $('#gold_medal_count').val(rule['gold_medal_count']);
                $('#follower_count').val(rule['follower_count']);
                $('#following_count').val(rule['following_count']);
                if (index == 0) {
                    $('#div_upload_video_count_per_trick_advanced').hide();
                    $('#div_view_count_per_video_advanced').hide();
                    $('#div_try_count_advanced').hide();
                    $('#div_dribble_average_advanced').hide();
                    $('#div_dribble_score').hide();

                } else {
                    $('#div_upload_video_count_per_trick_advanced').show();
                    $('#div_view_count_per_video_advanced').show();
                    $('#div_try_count_advanced').show();
                    $('#div_dribble_average_advanced').show();
                    $('#div_dribble_score').show();

                }
            }
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
                <h3><span class="text-semibold">Unlock Management</span></h3>
            </div>
        </div>

        <div class="breadcrumb-line">
            <ul class="breadcrumb">
                <li><a href="{{ URL::to('/admin/unlock_management')}}"><i class="icon-home2 position-left"></i>Unlock Management</a></li>
                
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
                    <form class="form-horizontal" action="{{url('/admin/unlock_rule/update_rule')}}" enctype="multipart/form-data" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="control-label col-lg-2"> Category</label>
                            <div class="col-lg-10">
                                <select class="select" name="category_id" required id="category_id">
                                    @foreach($unlock_rules as $rule)
                                        <option value="{{$rule->category_id}}">{{$rule->category_title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2"> Facebook Connect</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="facebook_connect" id="facebook_connect" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2"> Google Connect</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="google_connect" id="google_connect" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2"> Upload Video Count(amature)</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="upload_video_count_per_trick_amature" id="upload_video_count_per_trick_amature" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2"> View Count Per Video(amature)</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="view_count_per_video_amature" id="view_count_per_video_amature" required>
                            </div>
                        </div>
                        <div class="form-group" id="div_upload_video_count_per_trick_advanced" required>
                            <label class="control-label col-lg-2"> Upload Video Count(advanced)</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="upload_video_count_per_trick_advanced" id="upload_video_count_per_trick_advanced" required>
                            </div>
                        </div>
                        <div class="form-group" id="div_view_count_per_video_advanced" required>
                            <label class="control-label col-lg-2"> View Count Per Video(advanced)</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="view_count_per_video_advanced" id="view_count_per_video_advanced" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="control-label col-lg-2"> Try Count(amature)</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="try_count_amature" id="try_count_amature" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2"> Dribble Progress(amature)</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="dribble_average_amature" id="dribble_average_amature" required>
                            </div>
                        </div>
                        <div class="form-group" id="div_try_count_advanced">
                            <label class="control-label col-lg-2"> Try Count(advanced)</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="try_count_advanced" id="try_count_advanced" required>
                            </div>
                        </div>
                        <div class="form-group" id="div_dribble_average_advanced">
                            <label class="control-label col-lg-2"> Dribble Progress(advanced)</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="dribble_average_advanced" id="dribble_average_advanced" required>
                            </div>
                        </div>
                        <div class="form-group" id="div_dribble_score">
                            <label class="control-label col-lg-2"> Dribble Score</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="dribble_score" id="dribble_score" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2"> Gold Medal</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="gold_medal_count" id="gold_medal_count" id="gold_medal_count" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2"> Follower Count</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="follower_count" id="follower_count" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2"> Following Counts</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="following_count" id="following_count" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success pull-right col-lg-2">Update Rule<i class="icon-arrow-right14 position-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /content area -->
</div>
<!-- /main content -->
@endsection



