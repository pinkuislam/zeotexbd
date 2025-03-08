<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;
use App\Models\SMSModel;

class SMSService
{
    public static function sendSMS($type, $name, $message, $mobile_number)
    {
        $response = SMSService::send($mobile_number, $message);

        if (str_contains($response, 'Successful')) {
            $storeData = [
                'name' => $name,
                'datetime' => now(),
                'message' => $message,
                'mobile_number' => $mobile_number,
                'response' => $response,
                'sent_by' => Auth::user()->id
            ];
             SMSModel::create($storeData);
        }
    }

    public static function send($mobileNo, $message)
    {
        $ch = curl_init();
		$route_data_array = array(
			 'user' => 'oshni',
			 'pwd' => 'Oshni@123$',
			 'senderid' => '8809617642242',
			 'CountryCode' => '+880',
			 'mobileno' => $mobileNo,
			 'msgtext' => $message
			 ); 
		$route_final_data = http_build_query($route_data_array);
		$getUrl = "http://mshastra.com/sendurl.aspx?&".$route_final_data;
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $getUrl);
		curl_setopt($ch, CURLOPT_TIMEOUT, 80);
		$output = curl_exec($ch);
		$output_json = json_encode($output);
		curl_close($ch);
        return $output_json; //""1969907591,01751017812,Send Successful\r\n""
    }
}