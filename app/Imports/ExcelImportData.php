<?php

namespace App\Imports;

use App\Models\ExcelImport;
use Maatwebsite\Excel\Concerns\ToModel;

class ExcelImportData implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected $isFirstRow = true;

    public function model(array $row)
    {
        // Skip the first row (header)
        if ($this->isFirstRow) {
            $this->isFirstRow = false;
            return null; // Skip the first row
        }

        return new ExcelImport([
            'name' => $row[0] ?? null,
            'email' => $row[1] ?? null,
            'phone' => $row[2] ?? null,
            'address' => $row[3] ?? null,
        ]);
    }
}