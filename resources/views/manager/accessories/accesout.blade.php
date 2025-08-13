@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-head">
            <div class="container mt-3">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4 class="text-uppercase">List Accessories Out</h4>
                        </div>
                        <div class="col-sm-6">
                            <form id="filterForm">
                                <div class="float-end me-2">
                                    <select name="bulan" id="bulan" class="form-control">
                                        <option value="">Semua Bulan</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option
                                                value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="float-end me-2">
                                    <select name="tahun" id="tahun" class="form-control">
                                        <option value="">Semua Tahun</option>
                                        @for ($i = now()->year - 5; $i <= now()->year; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="float-end me-2">
                                    <select name="divisi" id="divisi" class="form-control">
                                        <option value="">Semua Divisi</option>
                                       @foreach ($divisi as $div  )
                                           <option value="{{ $div->id }}">{{ $div->name }}</option>
                                       @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="mb-3">
                    <button id="exportExcel" class="btn btn-success btn-sm">Download Excel</button>
                    <button id="exportPDF" class="btn btn-danger btn-sm">Download PDF</button>
                    <button id="printTable" class="btn btn-primary btn-sm">Print</button>
                </div>
                <table id="inout" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th class="text-center">Tanggal</th>
                            <th>Code Access</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Capital Price</th>
                            <th class="text-center" width="10%">Qty</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody id="accesout-data">
                        {{-- Data akan dimuat melalui AJAX --}}
                        </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('head')
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
      $(document).ready(function () {
        function loadData(bulan = '', tahun = '', divisi = '') {
        $.ajax({
            url: "{{ route('manager.acces.accesout') }}",
            type: "GET",
            data: { bulan: bulan, tahun: tahun, divisi: divisi },
            success: function (response) {
                    let tableRows = '';
                    $.each(response, function (accessoryId, data) {
                        tableRows += `
                             <tr class="stok-info">
                                <td colspan="2"><strong>Stok Awal:</strong> ${data.stok_awal}</td>
                                <td colspan="2"><strong>Total Keluar:</strong> ${data.total_keluar}</td>
                                <td colspan="2"><strong>Request:</strong> ${data.request_acces}</td>
                                <td colspan="2"><strong>Sisa Stok:</strong> ${data.stok_sisa}</td>
                            </tr>
                        `;
                        $.each(data.data, function (index, acces) {
                            tableRows += `
                                <tr class="data-row">
                                    <td>${acces.sale.invoice}</td>
                                    <td>${acces.acces_out}</td>
                                    <td>${acces.accessories.code_acces}</td>
                                    <td>${acces.accessories.name}</td>
                                    <td>${acces.accessories.price}</td>
                                    <td>${acces.accessories.capital_price}</td>
                                    <td>${acces.qty}</td>
                                    <td>${acces.total_price}</td>
                                </tr>
                            `;
                        });
                    });
                    $('#accesout-data').html(tableRows);
                },
                error: function () {
                    alert('Terjadi kesalahan saat memuat data.');
                }
            });
        }

        loadData();

        $('#filterForm select').on('change', function () {
            const bulan  = $('#bulan').val();
            const tahun  = $('#tahun').val();
            const divisi = $('#divisi').val();
            loadData(bulan, tahun, divisi);
        });


        $('#inout').before(`
            <div class="mb-2">
                <input type="text" id="manualSearch" class="form-control" placeholder="Cari data...">
            </div>
        `);

        $(document).on('keyup', '#manualSearch', function () {
            var keyword = $(this).val().toLowerCase();

            var $rows = $('#accesout-data tr');
            var currentGroup = null;
            var groupHasMatch = false;

            $rows.each(function () {
                if ($(this).hasClass('stok-info')) {
                    if (currentGroup) {
                        if (!groupHasMatch) {
                            currentGroup.hide();
                        }
                    }
                    currentGroup = $(this).show();
                    groupHasMatch = false;
                } else {
                    var text = $(this).text().toLowerCase();
                    if (keyword === '' || text.indexOf(keyword) > -1) {
                        $(this).show();
                        groupHasMatch = true;
                    } else {
                        $(this).hide();
                    }
                }
            });

            if (currentGroup && !groupHasMatch) {
                currentGroup.hide();
            }
        });

        function getFileName(baseName = "Laporan Accessories") {
            let bulanVal   = $('#bulan').val();
            let tahunVal   = $('#tahun').val();
            let divisiVal  = $('#divisi').val();

            let bulanNama  = bulanVal ? $('#bulan option:selected').text() : '';
            let divisiNama = divisiVal ? $('#divisi option:selected').text() : '';

            if (bulanVal && tahunVal && divisiVal) {
                return `${baseName} ${divisiNama} ${bulanNama} ${tahunVal}`;
            } else if (tahunVal && divisiVal) {
                return `${baseName} ${divisiNama} ${tahunVal}`;
            } else if (bulanVal && divisiVal) {
                return `${baseName} ${divisiNama} ${bulanNama}`;
            } else if (divisiVal) {
                return `${baseName} ${divisiNama}`;
            } else if (bulanVal && tahunVal) {
                return `${baseName} ${bulanNama} ${tahunVal}`;
            } else if (tahunVal) {
                return `${baseName} ${tahunVal}`;
            } else if (bulanVal) {
                return `${baseName} ${bulanNama}`;
            }
            return baseName;
            }


    // Export Excel
    $('#exportExcel').click(function () {
        let table = document.getElementById("inout");
        let wb = XLSX.utils.table_to_book(table, { sheet: "Accessories Out" });
        let fileName = getFileName("Laporan Aksesories") + ".xlsx";
        XLSX.writeFile(wb, fileName);
    });

    // Export PDF
    $('#exportPDF').click(function () {
        let headers = [];
        $('#inout thead th').each(function () {
            headers.push($(this).text());
        });

        let data = [];
    $('#accesout-data tr.data-row:visible').each(function () {
        let row = [];
        $(this).find('td').each(function () {
            row.push($(this).text());
        });
        data.push(row);
    });

        let fileName = getFileName("Laporan Aksesories") + ".pdf";
        let titleText = "Accessories Out";
        let filterName = getFileName("");
        if (filterName) {
            titleText += ` – ${filterName}`;
        }

        let docDefinition = {
            pageOrientation: 'landscape',
            content: [
                { text: titleText, style: 'header' },
                {
                    table: {
                        headerRows: 1,
                        widths: Array(headers.length).fill('*'),
                        body: [headers].concat(data)
                    }
                }
            ],
            styles: {
                header: { fontSize: 14, bold: true, margin: [0, 0, 0, 10] }
            }
        };

        pdfMake.createPdf(docDefinition).download(fileName);
    });

    // Print
    $('#printTable').click(function () {
        let filterName = getFileName("");
        let titleText = "Accessories Out";
        if (filterName) {
            titleText += ` – ${filterName}`;
        }

        let printWindow = window.open('', '', 'height=600,width=900');
        printWindow.document.write('<html><head><title>' + titleText + '</title></head><body>');
        printWindow.document.write('<h3>' + titleText + '</h3>');
        printWindow.document.write($('#inout')[0].outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });

    });
    </script>
@endpush
