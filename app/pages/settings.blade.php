@extends("admin/admin_app")

@section("css")

@endsection


@section("content")

<!-- Main content -->
<div class="content-wrapper">

    <!-- Page header -->
    <div class="page-header page-header-default">
        <div class="page-header-content">
            <div class="page-title">
                <h4><span class="text-semibold">Settings</span></h4>
            </div>
        </div>

        <div class="breadcrumb-line">
            <ul class="breadcrumb">
                <li><a href="{{ URL::to('/admin/settings/') }}"><i class="icon-cog2 position-left"></i> Settings</a></li>
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

                    <div class="tabbable">
                        <ul class="nav nav-tabs nav-tabs-bottom">
                            <li class="active"><a href="#bottom-tab1" data-toggle="tab">General</a></li>
                            <li class=""><a href="#bottom-tab2" data-toggle="tab">Terms Of Use</a></li>
                            <li class=""><a href="#bottom-tab3" data-toggle="tab">Privacy Policy</a></li>
                        </ul>

                        <div class="tab-content">

                            <div class="tab-pane active" id="bottom-tab1">
                                <form class="form-horizontal" action="{{URL::to('/admin/settings/editGeneralSetting')}}" method="post" enctype="multipart/form-data">

                                    {!! csrf_field() !!}

                                    <fieldset class="content-group">

                                        <div class="form-group">
                                            <label class="control-label col-lg-1">Site Name</label>
                                            <div class="col-lg-11">
                                                <input type="text" class="form-control" name="site_name" placeholder="Enter Site Name ..."
                                                       value="{{isset($setting->site_name) ? $setting->site_name : ''}}">
                                            </div>
                                        </div>
                                    </fieldset>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"> Save Changes <i class="icon-arrow-right14 position-right"></i></button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane" id="bottom-tab2">
                                    <form class="form-horizontal" action="{{URL::to('/admin/settings/editTermsOfUse')}}" method="post">

                                        {!! csrf_field() !!}

                                        <fieldset class="content-group">

                                            <div class="form-group">
                                                <label class="control-label col-lg-1">Terms Of Use</label>

                                                <div class="col-lg-11">
                                                    <textarea name="content" rows="8" cols="5" class="form-control" placeholder="">{{isset($setting->terms_of_use) ? $setting->terms_of_use : null}}</textarea>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary"> Save Changes <i class="icon-arrow-right14 position-right"></i></button>
                                        </div>
                                    </form>
                            </div>

                            <div class="tab-pane" id="bottom-tab3">
                                <form class="form-horizontal" action="{{URL::to('/admin/settings/editPrivacyPolicy')}}" method="post">

                                    {!! csrf_field() !!}

                                    <fieldset class="content-group">

                                        <div class="form-group">
                                            <label class="control-label col-lg-1">Privacy Policy</label>

                                            <div class="col-lg-11">
                                                <textarea name="content" rows="8" cols="5" class="form-control" placeholder="">{{isset($setting->privacy_policy) ? $setting->privacy_policy : null}}</textarea>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"> Save Changes <i class="icon-arrow-right14 position-right"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

    </div>
    <!-- /content area -->

</div>
<!-- /main content -->
@endsection


@section("js")
    
@endsection