<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Produk as ModelProduk;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Produk implements ToCollection, WithHeadingRow
{
    public function headingRow():int
    {
        return 1;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

    }
}
