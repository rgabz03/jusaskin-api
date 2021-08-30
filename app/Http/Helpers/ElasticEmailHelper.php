<?php

namespace App\Http\Helpers;

define('ELASTIC_API_KEY', '5A6E2956BDAC609941398194DA149B306F12E3B6DE821A929AB5B65D2334651E59DE8A47BC1071F049BD1365E9CBC0D5');
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

