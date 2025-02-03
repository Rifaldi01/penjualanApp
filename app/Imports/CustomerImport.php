<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Divisi;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Mendapatkan divisi_id pengguna yang sedang login
        $userDivisiId = Auth::user()->divisi_id;

        $errors = [];

        if (empty($row[1])) { // Assuming 'name' is in the second column
            $errors[] = 'Kolom name tidak boleh kosong';
        }

        if (empty($row[2])) { // Assuming 'phone_wa' is in the third column
            $errors[] = 'Kolom phone_wa tidak boleh kosong';
        }

        if (empty($row[5])) { // Assuming 'address' is in the sixth column
            $errors[] = 'Kolom address tidak boleh kosong';
        }

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        return new Customer([
            'divisi_id' => $userDivisiId, // Menggunakan divisi_id dari pengguna yang login
            'name' => $row[1],           // Nama pelanggan
            'phone_wa' => $row[2],       // Nomor WhatsApp
            'phone' => $row[3],          // Nomor telepon
            'company' => $row[4],        // Perusahaan
            'addres' => $row[5],         // Alamat
        ]);
    }
}
