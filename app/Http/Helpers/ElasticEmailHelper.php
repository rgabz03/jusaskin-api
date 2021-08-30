<?php

namespace App\Http\Helpers;

define('ELASTIC_API_KEY', '5E49B689F60B9971FB1EC8CDFAC957C12127AE6D250ADD8A20761D6B61AD36BEDDD730383B3509D7C435FD152A03B1DF');
define('COMPANY_NAME', 'Jusaskin.com');
define('ELASTIC_HTTP_URL', 'https://api.elasticemail.com/v2/email/send');
define('SUPPORT_EMAIL', 'mail@jusaskin.com');

class  ElasticEmailHelper extends Helper
{   
   
    public static function send(
        $subject,
        $content,
        $to = '',
        $from = '',
        $from_name = '',
        $bcc="",
        $useFooter = false,
        $useTemplate = true,
        $clean_subject="",
        $bcc_address = "",
        $attachments="",
        $channel="Default"
    )
    {

        $url =  ELASTIC_HTTP_URL;
        $filename = $attachments;
        $file_name_with_full_path = realpath('./'.$filename);
        $filetype = "text/plain"; // Change correspondingly to the file type

        try{
                $post = array('from' => $from,
                            'fromName' => $from_name,
                            'apikey' => ELASTIC_API_KEY,
                            'subject' => $subject,
                            'bodyHtml' => $content,
                            'bodyText' => $content,
                            'to' => $to,
                            'isTransactional' => false);
                            // 'file_1' => new CurlFile($file_name_with_full_path, $filetype, $filename));
                
                $ch = curl_init();
                    
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $post,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ));
                
                $result=curl_exec ($ch);
                curl_close ($ch);
                
                return $result;    
        }
        catch(Exception $ex){
            return $ex->getMessage();
        }
    }
}

