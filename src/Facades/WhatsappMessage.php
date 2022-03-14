<?php


namespace EmizorIpx\WhatsappMessages\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|array sendWhithTemplate( string $phone_number, string $template_name, array $body_params, array $media_params, array $buttons)
 * @method static object|array getStatusMessage( string|array $message_keys)
 */

 class WhatsappMessage extends Facade {


    protected static function getFacadeAccessor()
    {
        return 'send_message';
    }

 }