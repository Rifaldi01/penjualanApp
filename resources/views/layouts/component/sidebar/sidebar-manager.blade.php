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
                        Accessories Redy
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.accesKosong')}}">
                        <i class='bx bx-list-ul'></i>
                        Accessories Habis
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-import'></i>
                </div>
                <div class="menu-title">Barang Masuk</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.item.itemin')}}">
                        <i class='bx bx-radio-circle'></i>
                        Items
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.acces.accesin')}}">
                        <i class='bx bx-radio-circle'></i>
                        Accessories
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-export'></i>
                </div>
                <div class="menu-title">Barang Keluar</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.item.itemout')}}">
                        <i class='bx bx-radio-circle'></i>
                        Items
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.acces.accesout')}}">
                        <i class='bx bx-radio-circle'></i>
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
{{--        <li class="menu-label">Permintaan Barang</li>--}}
{{--        <li>--}}
{{--            @if(isset($notiff) && $notiff > 0)--}}
{{--                <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-2"><span class="visually-hidden">unread messages</span></span>--}}
{{--            @else--}}
{{--            @endif--}}
{{--            <a href="javascript:;" class="has-arrow">--}}
{{--                <div class="parent-icon">--}}
{{--                    <i class='bx bx-collection'></i>--}}
{{--                </div>--}}
{{--                <div class="menu-title">Request Accessories</div>--}}
{{--            </a>--}}
{{--            <ul>--}}
{{--                <li>--}}
{{--                    <a href="{{route('manager.permintaan.index')}}">--}}
{{--                        <i class='bx bx-arrow-to-bottom'></i>--}}
{{--                        Minta Accessories--}}
{{--                        @if($minta > 0)--}}
{{--                            <span class="badge bg-danger">{{$minta}}</span>--}}
{{--                        @else--}}
{{--                        @endif--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <a href="{{route('manager.permintaan.konfirmasi')}}">--}}
{{--                        <i class='bx bx-arrow-to-top'></i>--}}
{{--                        Permintaan Accessories--}}
{{--                        @if($notif > 0)--}}
{{--                            <span class="badge bg-danger">{{$notif}}</span>--}}
{{--                        @else--}}
{{--                        @endif--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        </li>--}}
        <li class="menu-label">PEMBELIAN BARANG</li>
        <li>
            <a href="{{route('manager.pembelian.index')}}">
                <div class="parent-icon"><i class='bx bx-arrow-to-bottom'></i>
                </div>
                <div class="menu-title">Pembelian</div>
            </a>
        </li>
        <li>
            <a href="{{route('manager.supplier.index')}}">
                <div class="parent-icon"><i data-feather="truck"></i>
                </div>
                <div class="menu-title">Supplier</div>
            </a>
        </li>
    </ul>
    <!--end navigation-->
</div>
