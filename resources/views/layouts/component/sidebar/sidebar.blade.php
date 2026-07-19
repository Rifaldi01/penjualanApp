<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{URL::to('assets/images/asd.png')}}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text"><i>S A</i><i style="color:black"> L E S</i></h4>
            {{--            <h4 class="logo-text"><i>DND</i><i style="color:black">SURVEY</i></h4>--}}
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        @if(Auth::user()->divisi_id == 6)
            <li>
                <a href="{{url('/gudang/dashboard')}}">
                    <div class="parent-icon"><i class='bx bx-home-alt'></i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
            <li class="menu-label">Permintaan Barang</li>
            <li>
                @if(isset($notiff) && $notiff > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-2"><span class="visually-hidden">unread messages</span></span>
                @else
                @endif
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon">
                        <i class='bx bx-collection'></i>
                    </div>
                    <div class="menu-title">Request Aksesoris</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.permintaan.index')}}">
                            <i class='bx bx-arrow-to-bottom'></i>
                            Minta Aksesoris
                            @if($minta > 0)
                                <span class="badge bg-danger">{{$minta}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.permintaan.konfirmasi')}}">
                            <i class='bx bx-arrow-to-top'></i>
                            Permintaan Aksesoris
                            @if($notif > 0)
                                <span class="badge bg-danger">{{$notif}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                @if(isset($notiffitem) && $notiffitem > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-2"><span class="visually-hidden">unread messages</span></span>
                @else
                @endif
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-box'></i>
                    </div>
                    <div class="menu-title">Request Alat</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.permintaanitem.index')}}">
                            <i class='bx bx-arrow-to-bottom'></i>
                            Minta Alat
                            @if($mintaitem > 0)
                                <span class="badge bg-danger">{{$mintaitem}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.permintaanitem.konfirmasi')}}">
                            <i class='bx bx-arrow-to-top'></i>
                            Permintaan Alat
                            @if($notifitem > 0)
                                <span class="badge bg-danger">{{$notifitem}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                </ul>
            </li>
        @else
            <!-- Tampilkan semua menu jika divisi_id bukan 6 -->
            <li>
                <a href="{{url('/gudang/dashboard')}}">
                    <div class="parent-icon"><i class='bx bx-home-alt'></i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
            <li>
                <a href="{{route('gudang.return.index')}}">
                    <div class="parent-icon"><i class='bx bx-refresh'></i>
                    </div>
                    <div class="menu-title"> Retur Transaksi</div>
                </a>
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
                        <a href="{{route('gudang.item.index')}}">
                            <i class='bx bx-box'></i>
                            Daftar Alat
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.cat.index')}}">
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
                        <a href="{{route('gudang.acces.editmultiple')}}">
                            <i class='bx bx-plus'></i>
                            Tambah Stok Aksesoris
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.acces.create')}}">
                            <i class='bx bx-barcode'></i>
                            Tambah Barcode Aksesoris
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.acces.index')}}">
                            <i class='bx bx-list-ul'></i>
                            Aksesoris Redy
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.accesKosong')}}">
                            <i class='bx bx-list-ul'></i>
                            Aksesoris Habis
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-error'></i>
                    </div>
                    <div class="menu-title">Aksesoris Rusak</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.acces.kembali')}}">
                            <i class='bx bx-plus'></i>
                            Tambah Aksesories Rusak
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.acces.listreject')}}">
                            <i class='bx bx-list-ul'></i>
                            Daftar Aksesoris Rusak
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
                        <a href="{{route('gudang.item.itemin')}}">
                            <i class='bx bx-radio-circle'></i>
                            Alat
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.acces.accesin')}}">
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
                        <a href="{{route('gudang.item.itemout')}}">
                            <i class='bx bx-radio-circle'></i>
                            Alat
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.acces.report')}}">
                            <i class='bx bx-radio-circle'></i>
                            Aksesoris
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{route('gudang.balance.index')}}">
                    <div class="parent-icon"><i class='bx bx-data'></i>
                    </div>
                    <div class="menu-title">Aksesoris Balance
                    </div>
                </a>
            </li>
{{--            <li>--}}
{{--                <a href="{{route('gudang.item.report')}}">--}}
{{--                    <div class="parent-icon"><i class='bx bx-file'></i>--}}
{{--                    </div>--}}
{{--                    <div class="menu-title">Report Item</div>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{route('gudang.acces.report')}}">--}}
{{--                    <div class="parent-icon"><i class='bx bx-file'></i>--}}
{{--                    </div>--}}
{{--                    <div class="menu-title">Report Accessories</div>--}}
{{--                </a>--}}
{{--            </li>--}}
            <li class="menu-label">Permintaan Barang</li>
            <li>
                @if(isset($notiff) && $notiff > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-2"><span class="visually-hidden">unread messages</span></span>
                @else
                @endif
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon">
                        <i class='bx bx-collection'></i>
                    </div>
                    <div class="menu-title">Request Aksesoris</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.permintaan.index')}}">
                            <i class='bx bx-arrow-to-bottom'></i>
                            Minta Aksesoris
                            @if($minta > 0)
                                <span class="badge bg-danger">{{$minta}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.permintaan.konfirmasi')}}">
                            <i class='bx bx-arrow-to-top'></i>
                            Permintaan Aksesoris
                            @if($notif > 0)
                                <span class="badge bg-danger">{{$notif}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.permintaan.retur')}}">
                            <i class='bx bx-repost'></i>
                            Retur Aksesoris
                            @if($notifretur > 0)
                                <span class="badge bg-danger">{{$notifretur}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                @if(isset($notiffitem) && $notiffitem > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-2"><span class="visually-hidden">unread messages</span></span>
                @else
                @endif
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-box'></i>
                    </div>
                    <div class="menu-title">Request Alat</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.permintaanitem.index')}}">
                            <i class='bx bx-arrow-to-bottom'></i>
                            Minta Alat
                            @if($mintaitem > 0)
                                <span class="badge bg-danger">{{$mintaitem}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.permintaanitem.konfirmasi')}}">
                            <i class='bx bx-arrow-to-top'></i>
                            Permintaan Alat
                            @if($notifitem > 0)
                                <span class="badge bg-danger">{{$notifitem}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.permintaanitem.retur')}}">
                            <i class='bx bx-repost'></i>
                            Retur Item
                            @if($notifreturitem > 0)
                                <span class="badge bg-danger">{{$notifreturitem}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                </ul>
            </li>
        @endif
    </ul>
    <!--end navigation-->
</div>
