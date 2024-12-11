<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{URL::to('assets/images/asd.png')}}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text"><i>DND</i><i style="color:black">SURVEY</i></h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{url('/superadmin/dashboard')}}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <li class="menu-label">Account</li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-user-plus'></i>
                </div>
                <div class="menu-title">Add User</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('superadmin.account.create')}}">
                        <i class='bx bx-circle '></i>
                        Ceate Account
                    </a>
                    <a href="{{route('superadmin.account.index')}}">
                        <i class='bx bx-circle '></i>
                        List Account
                    </a>
                </li>
            </ul>
        </li>

    </ul>
    <!--end navigation-->
</div>
