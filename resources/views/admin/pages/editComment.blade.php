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


@endsection


@section("content")
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header page-header-default">
            <div class="page-header-content">
                <div class="page-title">
                    <h3><span class="text-semibold">Comment Details</span></h3>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="#"><i class="icon-home2 position-left"></i>Comment</a></li>
                    @if(isset($comment))
                        <li class="active">{{ $comment->comment_id }}</li>
                    @endif
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
                        <form class="form-horizontal" action="@if (isset($comment)) {{url('/admin/users/'.$video_id.'/'.$comment->comment_id.'/update_comment')}} @endif" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label class="control-label col-lg-1"> Comment</label>
                                <div class="col-lg-11">
                                    <input type="textarea" class="form-control" placeholder="comment" name="message" value="{{isset($comment) ? $comment->message : ''}}" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success pull-right col-lg-2">Update<i class="icon-arrow-right14 position-right"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /content area -->
    </div>
    <!-- /main content -->
@endsection



