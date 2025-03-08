<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Sudip\MediaUploader\Facades\MediaUploader;
use Illuminate\Support\Str;

class CategoryImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        unset($rows[0]);
        foreach ($rows as $row) {
            $storeData = [
                'name' => $row[1],
                'status' => 'Active',
                'slug' => Str::slug($row[1]),
                'meta_title' => $row[3],
                'meta_keywords' => $row[4],
                'meta_description' => $row[5],
                'created_by' => Auth::user()->id ?? null,
            ];

            try {
                if ($row[1]) {
                    $file = MediaUploader::imageUploadFromUrl($row[2], 'categories', 1, null, [600, 600], [80, 80]);
                    $storeData['image'] = $file['name'];
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
            
            Category::firstOrCreate(['name' => $row[0]], $storeData);
        }
    }
}
