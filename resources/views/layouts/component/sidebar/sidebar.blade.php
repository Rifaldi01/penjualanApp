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
            <a href="{{url('/gudang/dashboard')}}">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
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
                    <a href="{{route('gudang.item.index')}}">
                        <i class='bx bx-box'></i>
                        Items
                    </a>
                </li>
                <li>
                    <a href="{{route('gudang.cat.index')}}">
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
                    <a href="{{route('gudang.acces.editmultiple')}}">
                        <i class='bx bx-plus'></i>
                        Add Accessories
                    </a>
                </li>
                <li>
                    <a href="{{route('gudang.acces.create')}}">
                        <i class='bx bx-barcode'></i>
                        Add Barcode
                    </a>
                </li>
                <li>
                    <a href="{{route('gudang.acces.index')}}">
                        <i class='bx bx-list-ul'></i>
                        Accessories
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{route('gudang.item.report')}}">
                <div class="parent-icon"><i class='bx bx-file'></i>
                </div>
                <div class="menu-title">Report Item</div>
            </a>
        </li>
        <li>
            <a href="{{route('gudang.acces.report')}}">
                <div class="parent-icon"><i class='bx bx-file'></i>
                </div>
                <div class="menu-title">Report Accessories</div>
            </a>
        </li>
    </ul>
    <!--end navigation-->
</div>