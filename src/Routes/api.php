<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => "\EmizorIpx\WhatsappMessages\Http\Controllers\Api", 'prefix' => 'whatsapp'], function () {

    Route::get('/', function () {
        return 'Whatsapp Library OK.....';
    });

});
