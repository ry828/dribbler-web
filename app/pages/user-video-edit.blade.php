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
                <h3><span class="text-semibold">Video Details</span></h3>
            </div>
        </div>

        <div class="breadcrumb-line">
            <ul class="breadcrumb">
                <li><a href="{{ URL::to('/admin/users/index') }}"><i class="icon-home2 position-left"></i>Users</a></li>
                <li><a href="{{ URL::to('/admin/users/' . $user->id . '/edit') }}">{{ $user->id }}</a></li>
                <li><a href="{{ URL::to('/admin/users/' . $user->id . '/videos') }}">Videos</a></li>
                <li><a href="{{ URL::to('admin/users/' . $video->video_id . '/edit_video')}}">{{ $video->video_id}}</a></li>
                
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
                    <form class="form-horizontal" action="{{url('/admin/users/'. $video->video_id .'/update_video')}}" enctype="multipart/form-data" method="post">
                        {{ csrf_field() }}
                        
                         <div class="form-group">
                            <label class="control-label col-lg-2">Video ID</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" type="number" placeholder="video id" name="video_id" step="1" readonly="readonly" value="{{isset($video) ? $video->video_id : ''}}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2">Thumbnail</label>
                            <div class="col-lg-10">
                                <input type="file" class="file-input" name="thumbnail" accept=".png, .jpg" data-allowed-file-extensions='["png", "jpg"]' data-show-caption="true" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2">LD Video</label>
                            <div class="col-lg-10">
                                <input type="file" class="file-input" name="ld_video" accept=".mp4" data-allowed-file-extensions='["mp4"]' data-show-caption="true" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-lg-2">HD Video</label>
                            <div class="col-lg-10">
                                <input type="file" class="file-input" name="hd_video" accept=".mp4" data-allowed-file-extensions='["mp4"]' data-show-caption="true" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success pull-right col-lg-2">Update Video <i class="icon-arrow-right14 position-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /content area -->
</div>
<!-- /main content -->
@endsection



