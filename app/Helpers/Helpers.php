<?php

use Illuminate\Contracts\Validation\Validator;

if ( ! function_exists('validatorErrorResponse')) {
    function validatorErrorResponse(Validator $validator)
    {
        return response()->json(['success' => false, 'message' => implode(", ", $validator->messages()->all())], 422);
    }
}

if ( ! function_exists('userRoles')) {
    function userRoles()
    {
        $array = ['Admin','Reseller', 'Reseller Business', 'Seller', 'Staff','Delivary Boy'];
        return $array;
    }
}
if ( ! function_exists('paymentTag')) {
    function paymentTag()
    {
        $array = ['Customer', 'Supplier', 'Sales-Reference-Agent', 'Sales-Commission-Agent', 'Transport-Agent','Owner','Labor','Loan-Holder','Expense','Income','Vat','Tax','Duties','Salary','Salary-Advance','Others','ait'];
        return $array;
    }
}

if ( ! function_exists('masterTypes')) {
    function masterTypes()
    {
        $array = ['Cover', 'Other'];
        return $array;
    }
}



if ( ! function_exists('productTypes')) {
    function productTypes()
    {
        $array = ['Fabric','Base','Product','Combo','Grey','Base-Ready-Production'];
        return $array;
    }
}

if ( ! function_exists('coverTypes')) {
    function coverTypes()
    {
        $array = ['Fabric', 'Base', 'Combo','Grey','Base-Ready-Production'];
        return $array;
    }
}

if ( ! function_exists('otherTypes')) {
    function otherTypes()
    {
        $array = ['Product', 'Combo'];
        return $array;
    }
}

if ( ! function_exists('categoryTypes')) {
    function categoryTypes()
    {
        $array = ['Regular', 'Victoria'];
        return $array;
    }
}



if ( ! function_exists('printHeader')) {
    function printHeader($title = null)
    {
        $html = '<div style="width:100%; font-size:18px;">
                        <div style="width:10%;float;float: left;">
                            <img src="'.asset('assets/img/logo.png').'" style="width:100%">
                        </div>
                        <div style="width:10%;float:left;"><p></p></div>
                        <div style="width:70%;float: left;line-height:8px;font-size:10px;">
                            <p style="color:#cc8419;font-weight:bold">ZEO TEX BD</p>
                            <p>14 no, ZEO Tex bd, Pakuria Bazar, Dhaka 1230</p>
                            <p>Mobile :+8801789-593255</p>
                            <p>E-mail: zeotexbd@gmail.com</p>
                            <p>Web: https://zeotexbd.com </p>
                        </div>
                </div>';

        if ($title) {
            $html .= '<div style="clear:both; padding: 10px 0; width:100%; text-align:center; font-size:18px;">'.$title.'</div>';
        }
        return $html;
    }
}

//url with query string
if ( ! function_exists('qUrl')) {
    function qUrl($queryArr = null, $route = null)
    {
        $route = $route ?? url()->current();
        return $route.qString($queryArr);
    }
}

//Search string get and set an url
if ( ! function_exists('qString')) {
    function qString($queryArr = null)
    {
        if (!empty($queryArr)) {
            $query = '';

            if (!empty($_GET)) {
                $getArray = $_GET;
                unset($getArray['page']);

                foreach ($queryArr as $qk => $qv) {
                    unset($getArray[$qk]);
                }

                $x = 0;
                foreach ($getArray as $gk => $gt) {
                    $query .= ($x != 0) ? '&' : '';
                    $query .= $gk.'='.$gt;
                    $x++;
                }
            }

            $y = 0;
            foreach ($queryArr as $qk => $qv) {
                if ($qv != null) {
                    $query .= ($y != 0 || $query != '') ? '&' : '';
                    $query .= $qk.'='.$qv;
                    $y++;
                }
            }

            return '?'.$query;

        } elseif (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != null) {
            return '?'.$_SERVER['QUERY_STRING'];
        }
    }
}

//Search Aray get to route redirect with get param
if ( ! function_exists('qArray')) {
    function qArray()
    {
        if (isset($_SERVER['QUERY_STRING'])) {
            return $_GET;
        } else {
            return null;
        }
    }
}

//Pagination per page
if ( ! function_exists('paginations')) {
    function paginations()
    {
        return ['100', '200', '300', '500'];
    }
}
//Reports Pagination per page
if ( ! function_exists('reportPaginations')) {
    function reportPaginations()
    {
        return ['500','1000','1500', '2000', '2500','3000', '5000'];
    }
}
if ( ! function_exists('paginateLimit')) {
    function paginateLimit()
    {
        return request()->limit ?? config('blade-components.paginate_default_limit');
    }
}
//Pagination serial number
if (!function_exists('pagiSerial')) {
    function pagiSerial($records)
    {
        $perPage = paginateLimit();
        return (!empty(request()->page)) ? (($perPage * (request()->page - 1)) + 1) : 1;
    }
}

//Pagination Message...
if ( ! function_exists('pagiMsg')) {
    function pagiMsg($data)
    {
        $msg = 'Showing ';
        $msg .= (($data->currentPage()*$data->perPage())-$data->perPage())+1;
        $msg .= ' to ';
        $msg .= ($data->currentPage()*$data->perPage()>$data->total()) ? $data->total() : $data->currentPage()*$data->perPage().' of '.$data->total();
        $msg .= ' row(s)';

        return $msg;
    }
}

//Date Format
if ( ! function_exists('dateFormat')) {
    function dateFormat($date, $time = null)
    {
        if ($date != null) {
            if ($time) {
                return date('d M, Y h:i A', strtotime($date));
            } else {
                return date('d M, Y', strtotime($date));
            }
        }
    }
}

//Time Format
if ( ! function_exists('timeFormat')) {
    function timeFormat($date)
    {
        return date('h:i A',(strtotime($date)));
    }
}

//Date Convert to DB Date Format
if ( ! function_exists('dbDateFormat')) {
    function dbDateFormat($date, $time = null)
    {
        if ($date != null) {
            if ($time) {
                return date('Y-m-d h:i A', strtotime($date));
            } else {
                return date('Y-m-d', strtotime($date));
            }
        }
    }
}

//DB Date Format Retrieve to Form Input Format
if ( ! function_exists('dbDateRetrieve')) {
    function dbDateRetrieve($date, $time = null)
    {
        if ($date != null) {
            if ($time) {
                return date('d-m-Y h:i A', strtotime($date));
            } else {
                return date('d-m-Y', strtotime($date));
            }
        }
    }
}

//Two Digit Number Format Function
if ( ! function_exists('numberFormat')) {
    function numberFormat($amount = 0, $coma = null)
    {
        if ($coma) {
            if ($amount == 0)
                return '-';
            else
                return number_format($amount, 2);
        } else {
            return number_format($amount, 2, '.', '');
        }
    }
}

//Showing limited text with '...'
if ( ! function_exists('excerpt')) {
    function excerpt($text, $limit = 200)
    {
        if (strlen(strip_tags($text)) > $limit) {
            return substr(strip_tags($text), 0, $limit).'...';
        } else {
            return strip_tags($text);
        }
    }
}

// For image view if image exists with lightbox (yes/no).
// ['thumb' => 1, 'popup' => 1, 'class' => '', 'style' =>'', 'fake' => 'avatar']
if ( ! function_exists('viewImg')) {
    function viewImg($path, $name, $array = null)
    {
        $path = 'storage/'.$path;
        $thumb = (isset($array['thumb']))?'thumb/':'';
        $class = (isset($array['class']))?'class="'.$array['class'].'"':'';
        $id = (isset($array['id']))?'id="'.$array['id'].'"':'';
        $style = (isset($array['style']))?'style="'.$array['style'].'"':'';
        $title = (isset($array['title']))?$array['title']:'';
        if ($name!= '' && file_exists($path.'/'.$thumb.$name)) {
            $path = url('/'.$path).'/';
            if (isset($array['popup'])) {
                return '<a href="'.$path.$name.'" data-fancybox="group" data-fancybox data-caption="'.$title.'" class="lytebox" data-lyte-options="group:vacation"><img src="'.$path.$thumb.$name.'" alt="'.$title.'" '.$class.$id.' '.$style.'></a>';
            } else {
                return '<img src="'.$path.$thumb.$name.'" alt="'.$title.'" '.$class.$id.' '.$style.'>';
            }
        } else {
            if (isset($array['fake'])) {
                return '<img src="'.url('/admin-assets/images/'.$array['fake']).'.png" alt="'.$array['fake'].'" '.$class.$id.' '.$style.'>';
            } else {
                return '';
            }
        }
    }
}

//For file view
if ( ! function_exists('viewFile')) {
    function viewFile($path, $name)
    {
        $path = 'storage/'.$path;
        if ($name != null && file_exists($path.'/'.$name)) {
            return '<a href="'.url('/'.$path.'/'.$name).'" class="link" target="_blank">'.$name.'</a>';
        } else {
            return '';
        }
    }
}

//For file view
if ( ! function_exists('urlToLink')) {
    function urlToLink($data, $name = null)
    {
        if ($data != null) {
            return '<a href="' . $data . '" class="link" target="_blank">' . ($name ?? $data) . '</a>';
        } else {
            return '';
        }
    }
}
