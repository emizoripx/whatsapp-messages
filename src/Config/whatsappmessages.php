<?php

return [
    
    /**
     * 
     * Host Whatsapp
     * 
     */
    'host_whatsapp' => env('WHATSAPP_HOST', 'https://us1.whatsapp.api.sinch.com'),
    /**
     * 
     * Token Whatsapp
     * 
     */
    'token_whatsapp' => env('WHATSAPP_TOKEN', ''),
    /**
     * 
     * Bot ID Whatsapp
     * 
     */
    'bot_id_whatsapp' => env('WHATSAPP_BOT_ID', ''),
    /**
     * 
     * Whatsapp Callback URL
     * 
     */
    'callback_url_whatsapp' => env('WHATSAPP_CALLBACK', ''),
    /**
     * 
     * Whatsapp Callback URL
     * 
     */
    's3_bucket' => env('AWS_BUCKET', ''),



];