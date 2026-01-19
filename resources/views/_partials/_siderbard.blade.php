<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        <div class="user-box text-center">
            <img src="{{asset('storage/uploads/'.auth()->user()->photo)}}" alt="user-img" title="{{auth()->user()->name}}" class="rounded-circle img-thumbnail avatar-md">
            <div class="dropdown">
                <a href="#" class="user-name dropdown-toggle h5 mt-2 mb-1 d-block"
                   aria-expanded="false">{{ auth()->user()->name }}</a>
            </div>

            <p class="text-muted left-user-info">{{ auth()->user()->role }}</p>

            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="{{route('profil')}}" class="text-muted left-user-info">
                        <i class="mdi mdi-cog"></i>
                    </a>
                </li>

                <li class="list-inline-item">
                    <a href="{{route('destroy')}}">
                        <i class="mdi mdi-power"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title">Navigation</li>

                <li>
                    <a href="{{route('dashboard')}}">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span> Dashboard </span>
                    </a>
                </li>
                @if(auth()->user()->user_type=='admin')
                    <li>
                        <a href="{{route('category.tags')}}">
                            <i class="mdi mdi-tag"></i>
                            <span> Tags </span>
                        </a>
                    </li>
                <li>
                    <a href="{{route('category.index')}}">
                        <i class="mdi mdi-box"></i>
                        <span> Categories </span>
                    </a>
                </li>
                    <li>
                        <a href="{{route('post.index')}}">
                            <i class="mdi mdi-post"></i>
                            <span> Posts </span>
                        </a>
                    </li>
                @endif


            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
