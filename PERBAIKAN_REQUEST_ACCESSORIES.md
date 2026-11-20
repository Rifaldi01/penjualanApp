# Perbaikan Request Accessories (Role Gudang)

File yang diperbaiki:
- `app/Http/Controllers/Gudang/PermintaanController.php`

Perubahan pada proses `approve()`:
1. Memproses pengurangan stok asal dan penambahan stok tujuan dalam satu database transaction.
2. Mengunci baris permintaan dan stok accessories (`lockForUpdate`) agar proses bersamaan tidak membaca/mengubah stok secara tidak konsisten.
3. Menggabungkan detail dengan `accessories_id` yang sama sebelum validasi dan mutasi stok, sehingga qty dari baris duplikat tidak lolos validasi secara terpisah.
4. Pengurangan stok memakai kondisi `stok >= totalQty`; bila gagal, seluruh transaksi dibatalkan.
5. Mencegah permintaan berstatus selain `pending` atau `disetujui` diproses ulang.
6. Status `diterima` hanya disimpan setelah seluruh item berhasil dipindahkan.
7. Pencocokan barang tujuan tetap memakai `code_acces` dan `divisi_id`.

## Penerapan

Salin file controller hasil perbaikan ke lokasi yang sama pada aplikasi. Tidak ada perubahan struktur database atau migration.

Jalankan pemeriksaan:

```bash
php -l app/Http/Controllers/Gudang/PermintaanController.php
php artisan route:list --name=gudang.permintaan
```

## Catatan data lama

Perbaikan kode tidak otomatis mengoreksi transaksi lama yang sudah berstatus `diterima`. Sebelum mengubah stok transaksi lama, bandingkan stok aktual divisi asal dan tujuan serta catatan `accessories_ins` dengan kode `PMT-` berdasarkan ID permintaan. Jangan mengurangi stok asal lagi tanpa verifikasi karena dapat menyebabkan pengurangan ganda.
