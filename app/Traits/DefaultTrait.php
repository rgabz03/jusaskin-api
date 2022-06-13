<?php

namespace App\Traits;
use Illuminate\Support\Str;
use JWTAuth;

trait DefaultTrait {

    /**
     * Check browser type
     */
    public function checkBrowser() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $deviceType = "desktop";

        if(preg_match("/Android|iPhone|BlackBerry|Windows Phone|iPad|iPod|Tablet/i", $userAgent)) {
            $deviceType = "mobile";
        }

        return $deviceType;
    }

    /**
	  * Generates a random key to pair with each entry
	  *
	 */
	public function generate_key($limit=4) {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, $limit);
    }

    public function _generate_key($length=5, $str='') {
        for($i=1; $i<=$length; $i++) {
            $ord = rand(48, 90);
            if( ( ($ord>=48) && ($ord<=57) ) || ( ($ord>=65) && ($ord<=90) ) ) {
                $str .= chr($ord);
            }
            else {
                $str .= $this->_generate_key(1);
            }
        }
        return $str;
    }

    public function _generateKey($limit = 32) {
        return Str::random($limit);
    }

    public function user_data($request)
    {
        # code...
        $user_data = false;
        if($request->header('Authorization'))
        {
            $user_data = JWTAuth::parseToken()->authenticate();
        }

        return $user_data;
    }

    public function hmac($key, $data)
    {

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }

        $key    = str_pad($key, $b, chr(0x00));
        $ipad   = str_pad('', $b, chr(0x36));
        $opad   = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*", md5($k_ipad . $data)));

    }

    public function sanitizeImage($image) {
        $sanitizer = new Sanitizer();
        $dirtySVG = file_get_contents($image);
        return $sanitizer->sanitize($dirtySVG);
    }

    public function mynimoEmailDetection($email)
    {
        # code...
        $pattern = "/@mynimo.com/i";
        return preg_match($pattern, $email);
    }

    public function munny($amt)
    {
        return number_format($amt,2);
    }

    public function convertNumber($number, $tag=1)  {

        if (($number < 0) || ($number > 999999999))
        {
        //throw new Exception("Number is out of range");
        }

        $cents = explode('.', $number);
        $cents = isset($cents[1]) ? $cents[1] :"";

        $Gn = floor($number / 1000000);  /* Millions (giga) */
        $number -= $Gn * 1000000;
        $kn = floor($number / 1000);     /* Thousands (kilo) */
        $number -= $kn * 1000;
        $Hn = floor($number / 100);      /* Hundreds (hecto) */
        $number -= $Hn * 100;
        $Dn = floor($number / 10);       /* Tens (deca) */
        $n = $number % 10;               /* Ones */
        $Mn = floor($number / 1);
        $Mc = $cents == "" ? 0:floor($cents/1);

        $res = "";
        if ($Gn)
        {
                $res .= $this->convertNumber($Gn,0) . " Million";
        }

        if ($kn)
        {
                $res .= (empty($res) ? "" : " ") . $this->convertNumber($kn,0) . " Thousand";
        }

        if ($Hn)
        {
                $res .= (empty($res) ? "" : " ") . $this->convertNumber($Hn,0) . " Hundred";
        }

        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
                "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen",
                "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen",
                "Nineteen");
        $tens = array("", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty",
                "Seventy", "Eighty", "Ninety");

        if ($Dn || $n)
        {
                if (!empty($res))
                {
                        $res .= " ";
                }

                if ($Dn < 2)
                {
                        $res .= $ones[$Dn * 10 + $n];
                }
                else
                {
                        $res .= $tens[$Dn];

                        if ($n)
                        {
                                $res .= " " . $ones[$n];
                        }
                }
        }

        if( $cents ) {
            if($Gn==0 && $kn==0 && $Dn==0 && $Hn==0 && $Mn == 0){
                $res .= (isset($ones[$cents])? (isset($tens[$cents])? $tens[$cents] : ""): $tens[$cents[0]]). ' '. (!isset($cents[1]) ? "" : (isset($ones[$cents])? $ones[$cents]: ($ones[isset($cents[1])?$cents[1]:0])) ). (($cents == '01') ? (isset($tens[$cents]) ? " Centavos" : " Centavo"): " Centavos");
            }else{
                $res .= (empty($res) ? "" : " ") .(($res == 'One')? " Peso " :" Pesos "). ($cents == 0 ? "":  "and ". (isset($ones[$cents])? (isset($tens[$cents])? $tens[$cents] : ""): $tens[$cents[0]]). ' '. (!isset($cents[1]) ? "" : (isset($ones[$cents])?$ones[$cents]: ($ones[isset($cents[1])?$cents[1]:0]))). (($cents == '01') ? (isset($tens[$cents]) ? " Centavos" : " Centavo"): " Centavos"));
            }
        }else{
            if($tag) $res .= (($res=="One")?" Peso":" Pesos");
        }


        if (empty($res) || ($Gn==0 && $kn==0 && $Dn==0 && $Hn==0 && $Mn == 0 && $Mc == 0))
        {
            $res = "zero";
        }

        return $res;
    }

}
