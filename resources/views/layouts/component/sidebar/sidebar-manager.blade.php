<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{URL::to('assets/images/asd.png')}}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text"><i>S A</i><i style="color:black"> L E S</i></h4>
            {{--            <h4 class="logo-text"><i>DND</i><i style="color:black">SURVEY</i></h4>--}}
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
                <div class="menu-title">Transaksi</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.sale.create')}}">
                        <i class='bx bx-money'></i>
                       Transaksi Baru
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.sale.index')}}">
                        <i class='bx bx-download'></i>
                        Transaksi
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.sale.return.index')}}">
                        <i class='bx bx-refresh'></i>
                        Retur Transaction
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='lni lni-users'></i>
                </div>
                <div class="menu-title">Pelanggan</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.customer.create')}}">
                        <i class='bx bx-user-plus'></i>
                        Register Pelanggan
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.customer.index')}}">
                        <i class='bx bx-list-ul'></i>
                        Daftar Pelanggan
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-label">Items</li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-box'></i>
                </div>
                <div class="menu-title">Alat</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.item.index')}}">
                        <i class='bx bx-box'></i>
                        Daftar Alat
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.cat.index')}}">
                        <i class='bx bx-category'></i>
                        Kategori Alat
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-collection'></i>
                </div>
                <div class="menu-title">Aksesoris</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('manager.acces.editmultiple')}}">
                        <i class='bx bx-plus'></i>
                        Tambah stok Aksesoris
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.acces.create')}}">
                        <i class='bx bx-barcode'></i>
                        Tambah Barcode
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.acces.index')}}">
                        <i class='bx bx-list-ul'></i>
                        Aksesoris Redy
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.accesKosong')}}">
                        <i class='bx bx-list-ul'></i>
                        Aksesoris Habis
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{route('manager.balance.index')}}">
                <div class="parent-icon"><i class='bx bx-data'></i>
                </div>
                <div class="menu-title">Aksesoris Balance
                </div>
            </a>
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
                        Alat
                    </a>
                </li>
                <li>
                    <a href="{{route('manager.acces.accesin')}}">
                        <i class='bx bx-radio-circle'></i>
                        Aksesoris
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
                        Alat
                    </a>
                </li>
                <li>
{{--                    <a href="{{route('manager.acces.accesout')}}">--}}
                    <a href="{{route('manager.acces.report')}}">
                        <i class='bx bx-radio-circle'></i>
                        Aksesoris
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{route('manager.report.index')}}">
                <div class="parent-icon"><i class='bx bx-file'></i>
                </div>
                <div class="menu-title">Laporan</div>
            </a>
        </li>
{{--        <li>--}}
{{--            <a href="javascript:;" class="has-arrow">--}}
{{--                <div class="parent-icon"><i class='bx bx-file'></i>--}}
{{--                </div>--}}
{{--                <div class="menu-title">Report</div>--}}
{{--            </a>--}}
{{--            <ul>--}}
{{--                <li>--}}
{{--                    <a href="{{route('manager.item.report')}}">--}}
{{--                        <i class='bx bx-circle'></i>--}}
{{--                        Report Item Sale--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <a href="{{route('manager.acces.report')}}">--}}
{{--                        <i class='bx bx-circle'></i>--}}
{{--                        Report Accessories Sale--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li>--}}
{{--                    <a href="{{route('manager.report.index')}}">--}}
{{--                        <i class='bx bx-circle'></i>--}}
{{--                        Report Sale--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            </ul>--}}
{{--        </li>--}}
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
