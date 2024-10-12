<?php

namespace App\Imports;

use App\Models\Customer;
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
        $errors = [];

        if (empty($row[0])) { // Assuming 'name' is in the first column
            $errors[] = 'Kolom name tidak boleh kosong';
        }

        if (empty($row[1])) { // Assuming 'phone_wa' is in the second column
            $errors[] = 'Kolom phone_wa tidak boleh kosong';
        }

        if (empty($row[4])) { // Assuming 'address' is in the third column
            $errors[] = 'Kolom address tidak boleh kosong';
        }

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        return new Customer([
            'name' => $row[0],
            'phone_wa' => $row[1],
            'phone' => $row[2],
            'company' => $row[3],
            'addres' => $row[4],
        ]);
    }
}
