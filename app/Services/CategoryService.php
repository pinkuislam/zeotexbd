<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public static function get($allLayer = false, $editId = null)
    {
        if (config('settings.category_layer') == 1 && !$allLayer) {
            return null;
        }

        $sql = Category::select('id', 'name', 'slug');
        if (config('settings.category_layer') > 1) {
            if ((config('settings.category_layer') == 3 && !$allLayer) || (config('settings.category_layer') >= 2 && $allLayer)) {
                $sql->with(['childs' => function($q) use($allLayer, $editId) {
                    $q->select('id', 'name', 'slug', 'parent_id');
                    if ($editId) {
                        $q->where('id', '!=', $editId);
                    }
                    if (config('settings.category_layer') == 3 && $allLayer) {
                        $q->with(['childs' => function($r) use($editId) {
                            $r->select('id', 'name', 'slug', 'parent_id');
                            if ($editId) {
                                $r->where('id', '!=', $editId);
                            }
                        }]);
                    }
                }]);
                $sql->whereNull('parent_id');
                if ($editId) {
                    $sql->where('id', '!=', $editId);
                }
            }
        }
        return $sql->where('status', 'Active')->get();
    }
}