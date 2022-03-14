<?php

namespace EmizorIpx\WhatsappMessages\Http\Controllers\Api;

use Illuminate\Http\Request;
use Carbon\Carbon;
use EmizorIpx\WhatsappMessages\Models\WhatsappMessage;
use EmizorIpx\WhatsappMessages\Utils\WhatsappMessageStates;
use Illuminate\Routing\Controller;

class WhatsappMessageController extends Controller
{
    

    public function callback(Request $request){

        \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>>>>>>> INICIO");
        
        $data = $request->all();
        
        \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>>>>>>> DATA: " . json_encode($data));

        if( isset($data['statuses']) ){
            $data = $data['statuses'][0];
            $message = WhatsappMessage::where('message_id', $data['message_id'])->first();

            if( ! is_null($message) ){
                \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>> UPDATE MESSAGE ID: " . $message->id );

                $data_update = [
                    "status" => $data['status'],
                    "state" => $data['state'],
                    "status_description" => WhatsappMessageStates::getDescriptionState($data['state']),
                    "error_details" => array_key_exists('details', $data) ? $data['details'] : null
                ];

                $data_update = array_merge($data_update, WhatsappMessageStates::setStateDate($data['state'], isset($data['timestamp']) ? $data['timestamp'] : null ));

                $message->update($data_update);
                

            } else {
                \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>> MENSAJE NO ENCONTRADO ");
            }

        }

        \Log::debug("WHATSAPP CALLBACK >>>>>>>>>>>>>>>>> FIN");
        return response()->json(['status' => 'success'], 200);

    }

}