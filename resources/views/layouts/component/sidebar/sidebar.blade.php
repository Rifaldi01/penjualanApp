<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{URL::to('assets/images/asd.png')}}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text"><i>DND</i><i style="color:black">SURVEY</i></h4>
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
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-collection'></i></div>
                    <div class="menu-title">Request Accessories</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.permintaan.index')}}">
                            <i class='bx bx-arrow-to-bottom'></i> Minta Accessories
                            @if($minta > 0)
                                <span class="badge bg-danger">{{$minta}}</span>
                            @endif
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-box'></i></div>
                    <div class="menu-title">Request Item</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.permintaanitem.index')}}">
                            <i class='bx bx-arrow-to-bottom'></i> Minta Item
                            @if($mintaitem > 0)
                                <span class="badge bg-danger">{{$mintaitem}}</span>
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
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-error'></i>
                    </div>
                    <div class="menu-title">Accessories Reject</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.acces.kembali')}}">
                            <i class='bx bx-plus'></i>
                            Reject
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.acces.listreject')}}">
                            <i class='bx bx-list-ul'></i>
                            List Reject
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
                            Items
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.acces.accesin')}}">
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
                        <a href="{{route('gudang.item.itemout')}}">
                            <i class='bx bx-radio-circle'></i>
                            Items
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.acces.accesout')}}">
                            <i class='bx bx-radio-circle'></i>
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
                    <div class="menu-title">Request Accessories</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.permintaan.index')}}">
                            <i class='bx bx-arrow-to-bottom'></i>
                            Minta Accessories
                            @if($minta > 0)
                                <span class="badge bg-danger">{{$minta}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.permintaan.konfirmasi')}}">
                            <i class='bx bx-arrow-to-top'></i>
                            Permintaan Accessories
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
                    <div class="menu-title">Request Item</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('gudang.permintaanitem.index')}}">
                            <i class='bx bx-arrow-to-bottom'></i>
                            Minta Item
                            @if($mintaitem > 0)
                                <span class="badge bg-danger">{{$mintaitem}}</span>
                            @else
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{route('gudang.permintaanitem.konfirmasi')}}">
                            <i class='bx bx-arrow-to-top'></i>
                            Permintaan Item
                            @if($notifitem > 0)
                                <span class="badge bg-danger">{{$notifitem}}</span>
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
