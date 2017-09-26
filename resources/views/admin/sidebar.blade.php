<div class="sidebar sidebar-main">
    <div class="sidebar-content">
        <!-- Main navigation -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">
                    <li class="navigation-header"><span>Main</span> <i class="icon-menu" title="" data-original-title="Main pages"></i></li>

                    <!-- User Page -->
                    <li class="{{ Request::is('admin/users*')? 'active': '' }}">
                        <a href="{{URL::to('admin/users')}}"><i class="icon-people"></i> <span>User Management</span></a>
                    </li>

                    <!-- Categories Page -->
                    <li class="{{ Request::is('admin/categories*')? 'active': '' }}">
                        <a href="{{URL::to('admin/categories')}}"><i class="icon-grid"></i> <span>Categories</span></a>
                    </li>

                    <!-- Tricks Page -->
                    <li class="{{( Request::is('admin/tricks*') || Request::is('admin/tricks*') )? 'active': '' }}">
                        <a href="{{URL::to('admin/tricks')}}"><i class="icon-server"></i><span>Tricks</span></a>
                    </li>

                    <!-- Tags Page -->
                    <li class="{{ Request::is('admin/tags*')? 'active': '' }}">
                        <a href="{{URL::to('admin/tags')}}"><i class="icon-stars"></i> <span>Tags</span></a>
                    </li>

                    <!-- Payment Page -->
                    <li class="{{ Request::is('admin/transactions*')? 'active': '' }}">
                        <a href="{{URL::to('admin/transactions')}}"><i class="icon-coins"></i> <span>Transactions</span></a>
                    </li>

                    <!-- Unlock Page -->
                    <li class="{{ Request::is('admin/unlock*')? 'active': '' }}">
                        <a href="{{URL::to('admin/unlock_rule')}}"><i class="icon-file-locked2"></i> <span>Unlock Management</span></a>
                    </li>

                    <!-- Setting Page -->
                    <!-- <li class="{{ Request::is('admin/setting*')? 'active': '' }}">
                        <a href="{{URL::to('admin/setting')}}"><i class="icon-cog2"></i> <span>Setting</span></a>
                    </li> -->
                </ul>
            </div>
        </div>
        <!-- /main navigation -->
    </div>
</div>