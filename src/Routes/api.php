<?php

use EmizorIpx\PosInvoicingFel\Models\FelInvoice;
use EmizorIpx\WhatsappMessages\Facades\WhatsappMessage;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => "\EmizorIpx\WhatsappMessages\Http\Controllers\Api", 'prefix' => 'whatsapp'], function () {

    Route::post('callback', 'WhatsappMessageController@callback');



});
