<?php

namespace EmizorIpx\WhatsappMessages\Traits;

use EmizorIpx\WhatsappMessages\Models\WhatsappMessage;

trait WhatsappMessageTrait {

    public function whatsapp_message() {

        return $this->hasOne( WhatsappMessage::class, 'message_key', 'message_key' );

    }

}