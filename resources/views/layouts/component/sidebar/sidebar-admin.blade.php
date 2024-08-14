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
            <a href="{{url('admin/dashboard')}}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-money'></i>
                </div>
                <div class="menu-title">Transaction</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('admin.sale.create')}}">
                        <i class='bx bx-money'></i>
                        New Transaction
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.sale.index')}}">
                        <i class='bx bx-download'></i>
                        Transaction
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='lni lni-users'></i>
                </div>
                <div class="menu-title">Customer</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('admin.customer.create')}}">
                        <i class='bx bx-user-plus'></i>
                        Register Customer
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.customer.index')}}">
                        <i class='bx bx-list-ul'></i>
                        List Customer
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-label">Items</li>
        <li>
            <a href="{{route('admin.item.index')}}">
                <div class="parent-icon">
                    <i class='bx bx-box'></i>
                </div>
                <div class="menu-title">Items</div>
            </a>
        </li>
        <li>
            <a href="{{route('admin.acces.index')}}">
                <div class="parent-icon">
                    <i class='bx bx-collection'></i>
                </div>
                <div class="menu-title">Accessories</div>
            </a>
        </li>
        <li>
            <a href="{{route('admin.item.sale')}}">
                <div class="parent-icon"><i class='bx bx-dollar'></i>
                </div>
                <div class="menu-title">Item Sale</div>
            </a>
        </li>
        <li>
            <a href="{{route('admin.acces.sale')}}">
                <div class="parent-icon"><i class='bx bx-dollar'></i>
                </div>
                <div class="menu-title">Accesories Sale</div>
            </a>
        </li>
        <li>
            <a href="{{route('admin.report.index')}}">
                <div class="parent-icon"><i class='bx bx-file'></i>
                </div>
                <div class="menu-title">Report</div>
            </a>
        </li>
    </ul>
    <!--end navigation-->
</div>
