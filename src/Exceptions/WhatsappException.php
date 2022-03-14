<?php

namespace EmizorIpx\WhatsappMessages\Exceptions;

use Exception;

class WhatsappException extends Exception
{
    public function __construct($msg, $exception_service = false)
    {
        $finalMessage = ['error' => ''];
        \Log::debug("WhastException >>>>>>>>>>>>>>> ");
        \Log::debug($msg);

        if ($msg != null) {
            $finalMessage = [
                'errors' => $exception_service ? json_decode($msg) : [
                    'message' =>  $msg
                ],
                'message_key' => ''
            ];
        }

        parent::__construct(json_encode($finalMessage));
    }
}
