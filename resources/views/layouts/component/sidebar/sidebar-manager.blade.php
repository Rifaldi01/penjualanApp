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
            <a href="{{url('manager/dashboard')}}">
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
                    <a href="{{route('manager.sale.create')}}">
                        <i class='bx bx-money'></i>
                        New Transaction
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.sale.index')}}">
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
                    <a href="{{route('manager.customer.create')}}">
                        <i class='bx bx-user-plus'></i>
                        Register Customer
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.customer.index')}}">
                        <i class='bx bx-list-ul'></i>
                        List Customer
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-label">Items</li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-box'></i>
                </div>
                <div class="menu-title">Items</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.item.index')}}">
                        <i class='bx bx-box'></i>
                        Items
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.cat.index')}}">
                        <i class='bx bx-category'></i>
                        Category
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-collection'></i>
                </div>
                <div class="menu-title">Accessories</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.acces.editmultiple')}}">
                        <i class='bx bx-plus'></i>
                        Add Accessories
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.acces.create')}}">
                        <i class='bx bx-barcode'></i>
                        Add Barcode
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.acces.index')}}">
                        <i class='bx bx-list-ul'></i>
                        Accessories
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-file'></i>
                </div>
                <div class="menu-title">Report</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.item.report')}}">
                        <i class='bx bx-circle'></i>
                        Report Item Sale
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.acces.report')}}">
                        <i class='bx bx-circle'></i>
                        Report Accessories Sale
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.report.index')}}">
                        <i class='bx bx-circle'></i>
                        Report Sale
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <!--end navigation-->
</div>
