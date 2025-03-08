<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromArray, WithHeadings
{
    protected $records;
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
    * @return \Illuminate\Support\Array
    */
    public function array(): array
    {
        $exports = [];
        foreach ($this->records as $key => $val) {
            $exports[] = [
                'parent_id' => optional($val->parent)->name,
                'name' => $val->name,
                'image' => $val->image_url,
                'creator' => $val->creator->name ?? '-',
                'status' => $val->status,
            ];
        }
        return $exports;
    }

    public function headings(): array
    {
        return ["Master Category", "Name", "Image Url", "Created By",'Status'];
    }
}
