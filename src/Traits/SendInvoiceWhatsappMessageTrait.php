<?php

namespace EmizorIpx\WhatsappMessages\Traits;

use EmizorIpx\WhatsappMessages\Exceptions\WhatsappException;
use EmizorIpx\WhatsappMessages\Models\FelWhatsappMessage;
use EmizorIpx\WhatsappMessages\Services\Whatsapp\WhatsappService;
use Carbon\Carbon;
use EmizorIpx\WhatsappMessages\Utils\WhatsappMessageStates;
use Exception;

trait SendInvoiceWhatsappMessageTrait {

    public function SendWhatsappMessage(){

        \Log::debug("Send Whatsapp Message >>>>>>>>>>>>>>>>>>>>> Number Phone: " . $this->telefonoCliente . "Client Name: " . $this->nombreRazonSocial);

        try{

            if( is_null($this->telefonoCliente) ){
                throw new WhatsappException(json_encode(["errors" => ["Número de teléfono requerido"]]));
            }

            $phone_number = $this->telefonoCliente;

            $invoice_whatsapp_message = FelWhatsappMessage::create([
                'company_id' => $this->restorant_id,
                'client_name' => $this->nombreRazonSocial == 'CONTROL TRIBUTARIO' ? 'Sin Nombre' : $this->nombreRazonSocial,
                'invoice_id' => $this->id,
                'user_id' => auth()->user()->id
            ]);

            \Log::debug("Send Whatsapp Message >>>>>>>>>>>>>>>>>>>>>>>>>> create service " );
            $whatsapp_service = new WhatsappService() ;

            $whatsapp_service->setNumber($phone_number);

            $invoice_whatsapp_message->update([
                'number_phone' => $phone_number
            ]);

            [$is_authorize, $failedToOptInNumbers ] = $whatsapp_service->authorizationOfSending();

            if($is_authorize){

                $data = [
                    "nit" => $this->complemento == null ? $this->numeroDocumento : $this->numeroDocumento . ' ' . $this->complemento,
                    "company_name" => $this->razonSocialEmisor,
                    "monto_total" => number_format($this->montoTotal,2),
                    "contact_key" => '',
                    "pdf_name" => "Factura". $this->numeroFactura . ".pdf",
                    "pdf_url" => $this->pdf_url
                ];

                $whatsapp_service->setData($data);

                $response = $whatsapp_service->sendMessage();

                $status_response =  $whatsapp_service->getStatusResponse();

                $invoice_whatsapp_message->update([
                    'status' => $status_response['status'],
                    'state' => $status_response['state'],
                    'message_id' => $status_response['message_id'],
                    'send_date' => Carbon::now()->toDateTimeString(),
                    'message' => json_encode($data)
                ]);

                if( $status_response['status'] == 'failure' ){
                    \Log::debug("Error al enviar el Mensaje");
                    throw new WhatsappException( json_encode(["errors" => ['Error al enviar el Mensaje']]));
                }

                $msg = WhatsappMessageStates::getDescriptionState($status_response['state']);

                $invoice_whatsapp_message->update([
                    'status_description' => $msg
                ]);

                return $msg;

            } else {

                $invoice_whatsapp_message->update([
                    'authorize_to_sent' => false,
                    'rejection_reason' => $failedToOptInNumbers['rejectionReason']
                ] );

                \Log::debug("Autorización de envío Denegada, Número: ");

                throw new WhatsappException( json_encode(["errors" => ["Autorización de envío Denegada, Número: " . $failedToOptInNumbers[0]['msisdn'] . " Razón: " . $failedToOptInNumbers[0]['rejectionReason']]]));

            }


        } catch( WhatsappException $ex ){

            $invoice_whatsapp_message->update([
                'errors' => $ex->getMessage()
            ] );

            \Log::debug($ex->getMessage());

            throw new Exception($ex->getMessage());

        }

    }

}