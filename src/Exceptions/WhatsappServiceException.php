<?php

namespace EmizorIpx\WhatsappMessages\Exceptions;

use Exception;

class WhatsappServiceException extends Exception
{
    public function __construct($msg)
    {
        $finalMessage = 'Errors';

        if ($msg != null) {
            $finalMessage = $msg;
        }

        parent::__construct($finalMessage);
    }
}
