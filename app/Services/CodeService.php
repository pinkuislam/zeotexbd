<?php

namespace App\Services;

class CodeService
{
    public static function generate($model, $prefix, $field)
    {
        $data = $model::select($field)->orderBy('id', 'DESC')->first();

        $prefixLength = strlen($prefix);
        $lastPart = $data ? intval(substr($data->$field, $prefixLength)) : 0;

        $number = $prefix;
        $number .= substr("0000000", 0, -strlen($lastPart + 1));
        $number .= $lastPart + 1;
        return $number;
    }
    public static function generateOrderCode($model, $prefix, $field)
    {
        $data = $model::select($field)->where('code', 'LIKE', '%' . $prefix . '%')->orderBy('id', 'DESC')->first();

        $prefixLength = strlen($prefix);
        $lastPart = $data ? intval(substr($data->$field, $prefixLength)) : 0;

        $number = $prefix;
        $number .= substr("00000000", 0, -strlen($lastPart + 1));
        $number .= $lastPart + 1;
        return $number;
    }

    public static function generateUserCode($type, $model, $field = null)
    {
        $prefix = '';
        
        if($type == 'Supplier'){
            $prefix = 'SUP';
        } else if($type == 'Admin') {
            $prefix = 'EMP';
        } else if($type == 'DyeingAgent') {
            $prefix = 'DA';
        } else if($type == 'Customer'){
            $prefix = 'CUS';
        } else if($type == 'Seller'){
            $prefix = 'SEl';
        } else if($type == 'Reseller'){
            $prefix = 'RES';
        } else if($type == 'Staff'){
            $prefix = 'EMP';
        }
        $data = $model::select($field)->orderBy('id', 'DESC')->first();

        $prefixLength = strlen($prefix);
        $lastPart = $data ? intval(substr($data->$field, $prefixLength)) : 0;

        $number = $prefix;
        $number .= substr("000000", 0, -strlen($lastPart + 1));
        $number .= $lastPart + 1;
        return $number;
    }
}