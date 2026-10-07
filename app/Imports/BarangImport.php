<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToArray, WithHeadingRow
{
    public function array(array $array): void
    {
        // empty, Excel::toArray will capture it
    }
}
