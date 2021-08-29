<?php

namespace App\Http\Controllers\API\V1;

header("Access-Control-Allow-Origin: *");

use JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ElasticEmailHelper;
use Illuminate\Http\Request;

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
            'support@jusaskin.com',
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
        $elasticEmailHelper = new ElasticEmailHelper();

        $subject =  "Subscriber";
        $content =  "Subscriber : $request->email";

        $send = $elasticEmailHelper->send(
            $subject,
            $content,
            'support@jusaskin.com',
            'support@jusaskin.com',
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
}
