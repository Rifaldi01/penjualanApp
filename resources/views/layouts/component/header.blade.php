<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3 bg-gradient-orange">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>
                <a href="avascript:;" class="btn d-flex align-items-center"></a>


            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">


                    <li class="nav-item dropdown dropdown-app">
                        <div class="dropdown-menu dropdown-menu-end p-0">
                            <div class="app-container p-2 my-2">
                            </div>
                        </div>
                    </li>

                    <li class="nav-item dropdown dropdown-large">

                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">

                            </a>
                            <div class="header-notifications-list">
                                <a class="dropdown-item" href="javascript:;">
                                </a>
                            </div>
                            <a href="javascript:;">
                            </a>
                        </div>
                    </li>
                    <li>
                        <div>
                            <a href="javascript:;"></a>
                            <div class="header-message-list">
                            </div>
                            <a href="javascript:;"></a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{asset('images/profile/'. Auth::user()->image)}}" class="user-img" alt="user avatar">
                    <div class="user-info">
                        <p class="user-name mb-0">{{Auth::user()->name}}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{route('profile.edit.edit', Auth::user()->id)}}">
                            <i class="bx bx-user fs-5"></i><span>Edit Profile</span></a>
                    </li>
                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
						document.getElementById('logout-form').submit();"><i class="bx bx-log-out-circle"></i><span>Logout</span></a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </ul>
            </div>
        </nav>
    </div>
</header>
