<?php

namespace EmizorIpx\WhatsappMessages\Utils;

use EmizorIpx\WhatsappMessages\Exceptions\WhatsappException;
use EmizorIpx\WhatsappMessages\Models\WhatsappMessage;
use EmizorIpx\WhatsappMessages\Services\Whatsapp\WhatsappService;
use Illuminate\Support\Str;
use Exception;
use Carbon\Carbon;
use EmizorIpx\WhatsappMessages\Exceptions\WhatsappServiceException;
use EmizorIpx\WhatsappMessages\Http\Resources\WhatsappMessageResource;

class WhatsappSendHelper {

    protected $whatsapp_service;

    public function __construct(WhatsappService $whatsapp_service)
    {
        $this->whatsapp_service = $whatsapp_service;
        
    }

    public function sendWhithTemplate( $phone_number, $template_name ,$body_params , $media_params , $buttons ){

        try{
            $message_key = strtoupper(str_replace( '.', '', uniqid('', true) )) ;

            if( is_null($phone_number) || empty($phone_number) ){
                throw new WhatsappException( "Número de teléfono requerido");
            }
            if( is_null($template_name) || empty($template_name) ){
                throw new WhatsappException( "Plantilla requerida");
            }
            

            $whatsapp_message = WhatsappMessage::create([
                'message_key' => $message_key,
                'number_phone' => $phone_number
            ]);

            $this->whatsapp_service->setNumber($phone_number);

            [$is_authorize, $failedToOptInNumbers ] = $this->whatsapp_service->authorizationOfSending();

            if($is_authorize){

                \Log::debug("Authorize to send >>>>>> ");

                $this->whatsapp_service->setTemplateName($template_name);

                $this->whatsapp_service->setBodyParams($body_params);

                if($media_params != null){
                    $this->whatsapp_service->setMediaParams($media_params);
                }

                if($buttons != null){
                    $this->whatsapp_service->setButtons($buttons);
                }


                $response = $this->whatsapp_service->sendMessage();

                $status_response =  $this->whatsapp_service->getStatusResponse();

                $whatsapp_message->update([
                    'status' => $status_response['status'],
                    'state' => $status_response['state'],
                    'message_id' => $status_response['message_id'],
                    'send_date' => Carbon::now()->toDateTimeString(),
                    'message' => json_encode($this->whatsapp_service->getPreparedData())
                ]);

                if( $status_response['status'] == 'failure' ){
                    \Log::debug("Error al enviar el Mensaje");
                    throw new WhatsappException( 'Error al enviar el Mensaje');
                }

                $msg = WhatsappMessageStates::getDescriptionState($status_response['state']);

                $whatsapp_message->update([
                    'status_description' => $msg
                ]);

                return ['message_key' => $message_key, 'message' => $msg];


            } else {
                $whatsapp_message->update([
                    'authorize_to_sent' => false,
                    'rejection_reason' => $failedToOptInNumbers['rejectionReason']
                ] );

                \Log::debug("Autorización de envío Denegada, Número: ");

                throw new WhatsappException( "Autorización de envío Denegada, Número: " . $failedToOptInNumbers[0]['msisdn'] . " Razón: " . $failedToOptInNumbers[0]['rejectionReason']);
            }

            

        } catch( WhatsappException | Exception $ex ){

            \Log::debug("Exception in Helpers " . $ex->getMessage() . "File: ". $ex->getFile() . " Line: " . $ex->getLine());

            if(isset($whatsapp_message)){
                $whatsapp_message->update([
                    'errors' => $ex->getMessage()
                ] );
            }
            $msg = $ex->getMessage();
            if( $ex instanceof WhatsappException ){
                $msg = json_decode ($msg);
                $msg->message_key = $message_key;
                $msg = json_encode($msg);
                \Log::debug("Error return " . $msg);
            }


            throw new WhatsappServiceException ($msg);

        }
    }

    public function getStatusMessage( $message_keys ){

        
        if( is_array($message_keys) ){

            $messages = WhatsappMessage::whereIn( 'message_key', $message_keys )->get();

            $data = WhatsappMessageResource::collection($messages);

        } else {

            $message = WhatsappMessage::where( 'message_key', $message_keys )->first();

            $data = new WhatsappMessageResource($message);

        }

        \Log::debug(json_encode($data));

        return $data;

    }

}