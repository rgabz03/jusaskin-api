<?php

namespace App\Http\Controllers\API\V1;

use JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ElasticEmailHelper;
use Illuminate\Http\Request;
use App\Mail\NotifyMe;
use Mail;

class TestController extends Controller
{
    //
    public function getInfo()
    {
        # code...
        return phpinfo();
    }

    public function testElasticMail()
    {
        # code...
        $elasticEmailHelper = new ElasticEmailHelper();

        $send = $elasticEmailHelper->send(
            "test",
            "Testing",
            'rgabz03@gmail.com',
            'mail@jusaskin.com',
            "Jusaskin.com",
            '',
            false,
            true,
            'clean subject',
            '',
            false,
            'Test');

            return ['sent' => $send];
    }


    public function testNotifyMe(Request $request)
    {
        # code...
        // $elasticEmailHelper = new ElasticEmailHelper();

        // $subject =  "Subscriber";
        // $content =  "Subscriber : $request->email";

        // $send = $elasticEmailHelper->send(
        //     $subject,
        //     $content,
        //     'mail@jusaskin.com',
        //     'mail@jusaskin.com',
        //     "Jusaskin.com",
        //     '',
        //     false,
        //     true,
        //     'clean subject',
        //     '',
        //     false,
        //     'Test');

        //     return ['sent' => $send];


        $request->validate([
            'email' => 'required|email',
            // 'subject' => 'required',
            // 'content' => 'required',
          ]);
  
          $data = [
            'subject' => 'Subscriber',
            'email' => $request->email,
          ];
  
          $send = Mail::send('mails.NotifyMe', $data, function($message) use ($data) {
            $message->to('mail@jusaskin.com')
            ->subject($data['subject']);
          });

          if($send){
            return back()->with(['message' => 'Email successfully sent!']);
          }
          
          return false;

    }
}
