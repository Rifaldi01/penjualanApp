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
                <div class="menu-title">Transaksi</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('admin.sale.create')}}">
                        <i class='bx bx-money'></i>
                        Transaksi Baru
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.sale.index')}}">
                        <i class='bx bx-download'></i>
                        Daftar Transaksi
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.return.index')}}">
                        <i class='bx bx-refresh'></i>
                        Retur Transaksi
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
                    <a href="{{route('admin.customer.create')}}">
                        <i class='bx bx-user-plus'></i>
                        Register Pelanggan
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.customer.index')}}">
                        <i class='bx bx-list-ul'></i>
                        Daftar Pelanggan
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-label">Alat</li>
        <li>
            <a href="{{route('admin.item.index')}}">
                <div class="parent-icon">
                    <i class='bx bx-box'></i>
                </div>
                <div class="menu-title">Daftar Alat</div>
            </a>
        </li>
        <li>
            <a href="{{route('admin.acces.index')}}">
                <div class="parent-icon">
                    <i class='bx bx-collection'></i>
                </div>
                <div class="menu-title">Aksesoris</div>
            </a>
        </li>

        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class='bx bx-edit'></i>
                </div>
                <div class="menu-title">Edit Harga</div>
            </a>
            <ul>
                <li>
                    <a href="{{route('admin.item.editItem')}}">
                        <i class='bx bx-box'></i>
                        Alat
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.acces.editAcces')}}">
                        <i class='bx bx-collection'></i>
                        Aksesoris
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{route('admin.item.sale')}}">
                <div class="parent-icon"><i class='bx bx-dollar'></i>
                </div>
                <div class="menu-title">Alat Terjual</div>
            </a>
        </li>
        <li>
            <a href="{{route('admin.acces.sale')}}">
                <div class="parent-icon"><i class='bx bx-dollar'></i>
                </div>
                <div class="menu-title">Aksesoris Terjual</div>
            </a>
        </li>
        <li>
            <a href="{{route('admin.report.index')}}">
                <div class="parent-icon"><i class='bx bx-file'></i>
                </div>
                <div class="menu-title">Laporan</div>
            </a>
        </li>
        <li class="menu-label">PEMBELIAN BARANG</li>
        <li>
            <a href="{{route('admin.pembelian.index')}}">
                <div class="parent-icon"><i class='bx bx-arrow-to-bottom'></i>
                </div>
                <div class="menu-title">Pembelian</div>
            </a>
        </li>
        <li>
            <a href="{{route('admin.supplier.index')}}">
                <div class="parent-icon"><i data-feather="truck"></i>
                </div>
                <div class="menu-title">Supplier</div>
            </a>
        </li>
    </ul>
    <!--end navigation-->
</div>
