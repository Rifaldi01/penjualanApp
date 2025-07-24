<?php
if (!function_exists('formatRupiah')) {
    /**
     * Format angka menjadi mata uang
     *
     * @param float $amount
     * @return string
     */
    function formatRupiah($amount)
    {
        return 'Rp. ' . number_format($amount, 0, '.', '.');
    }
}

function dateId($tanggal)
{
    // Pastikan tanggal dalam format yang diinginkan untuk diolah oleh Carbon
    $tanggal = \Carbon\Carbon::parse($tanggal);

    // Definisikan nama-nama hari dan bulan dalam bahasa Indonesia
    $namaHari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $namaBulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];

    $hari = $namaHari[$tanggal->format('l')];
    $bulan = $namaBulan[$tanggal->format('F')];
    $tanggalFormat = $tanggal->format('d');
    $tahun = $tanggal->format('Y');

    return "$hari, $tanggalFormat $bulan $tahun";
}

function tanggal($date)
{
    // Pastikan tanggal dalam format yang diinginkan untuk diolah oleh Carbon
    $tanggal = \Carbon\Carbon::parse($date);

    // Definisikan nama-nama hari dan bulan dalam bahasa Indonesia

    $namaBulan = [
        'January' => 'Jan',
        'February' => 'Feb',
        'March' => 'Mar',
        'April' => 'Apr',
        'May' => 'Mei',
        'June' => 'Jun',
        'July' => 'Jul',
        'August' => 'Ags',
        'September' => 'Sep',
        'October' => 'Okt',
        'November' => 'Nov',
        'December' => 'Des'
    ];

    $bulan = $namaBulan[$tanggal->format('F')];
    $tanggalFormat = $tanggal->format('d');
    $tahun = $tanggal->format('Y');

    return " $tanggalFormat $bulan $tahun";
}

