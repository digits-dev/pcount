<?php

namespace App\Imports;

use App\Models\UserCategoryTag;
use App\Models\WarehouseCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use CRUDBooster;

class UserCategoryTagImport implements ToModel, WithHeadingRow, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        UserCategoryTag::updateOrInsert([
            'category_tag_number' => $row['category_tag']
        ],[
            'user_name' => $row['user_name'],
            'category_tag_number' => $row['category_tag'],
            'warehouse_categories_id' => WarehouseCategory::withCategory($row['warehouse_category'])->id,
            'is_used' => ($row['is_used'] == "YES") ? 1 : 0,
            'status' => $row['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => CRUDBooster::myId()
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
