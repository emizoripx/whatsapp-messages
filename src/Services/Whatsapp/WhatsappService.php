<?php

namespace EmizorIpx\WhatsappMessages\Services\Whatsapp;

use EmizorIpx\WhatsappMessages\Exceptions\WhatsappException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WhatsappService {

    protected $number;

    protected $botId;

    protected $client;

    protected $template_name;

    protected $body_params;

    protected $media_params;

    protected $buttons;

    protected $prepared_data;

    protected $parsed_response;

    const QUEUED = 'queued';
    const DISPATCHED = 'dispatched';
    const SENT = 'sent';
    const DELIVERED = 'delivered';
    const READ = 'read';
    const DELETED = 'deleted';
    const FAILED = 'failed';
    const NO_OPT_IN = 'no_opt_in';
    const NO_CAPABILITY = 'no_capability';

    public function __construct()
    {
        \Log::debug("Data Cliente >>>>>>>>>>> " . 'Bearer ');
        $this->validateConfigs();
        $data_client['base_uri'] = config('whatsappmessages.host_whatsapp');
        $data_client['headers']['Authorization'] = 'Bearer ' . config('whatsappmessages.token_whatsapp');
        $data_client['headers']['Content-Type'] = 'application/json';


        $this->client = new Client($data_client);

        $this->botId = config('whatsappmessages.bot_id_whatsapp');
    }

    public function validateConfigs(){
        if(empty(config('whatsappmessages.token_whatsapp'))){
            throw new WhatsappException('Token no encontrado, se debe configurar en el .ENV');
        }
        if(empty(config('whatsappmessages.bot_id_whatsapp'))){
            throw new WhatsappException('Boot id no encontrado, se debe configurar en el .ENV');
        }
    }

    public function setNumber($number){
        \Log::debug("Set NUmber " . $number);

        $this->number = $number;

        \Log::debug($this->number);
    }

    public function setResponse($value){
        $this->parsed_response = $value;
    }

    public function setMediaParams($value){
        // [
        //     "type" => "document",
        //     "url" => "https://$bucket.s3.amazonaws.com/" . $this->data['pdf_url'],
        //     "filename" => $this->data['pdf_name']
        // ]
        \Log::debug("Set Media");
        $this->media_params = $value;
    }
    public function setButtons($value){
        // [[
        //     "type" => "url",
        //     "parameter" => $this->data['contact_key']
        // ]]
        \Log::debug("Set Buttons");
        $this->buttons = [$value];
        \Log::debug("Set Buttons >>>>>>> ");
    }

    public function setTemplateName($value){
        $this->template_name = $value;
    }

    public function setBodyParams($body_params){
        \Log::debug("Data to set");
        \Log::debug(json_encode($body_params));
        $this->body_params = $body_params;
        \Log::debug(json_encode($this->body_params));
    }

    public function getStatusResponse(){
        return $this->parsed_response['statuses'][0];
    }

    public function parse_response($response){
        
        if($response->getStatusCode() == 200 || $response->getStatusCode() == 201 ){

            return  json_decode( (string) $response->getBody(), true);

        } else if( $response->getStatusCode() == 400 ){
            \Log::debug("Bad Request");
            throw new WhatsappException( " 400: Bad Request ");

        } else if( $response->getStatusCode() == 401 ){
            \Log::debug("401: Unauthorized");
            throw new WhatsappException( " 401: Unauthorized");
        }

    }

    public function getPreparedData(){
        return $this->prepared_data;
    }


    public function prepared_data(){ //Add Option to send with media and buttons

        $array_data = [
            "to" => [$this->number],
            "message" => [
                "type" => "template",
                "template_name" => $this->template_name,
                "language" => "es"
            ],
            "callback" => env('APP_URL') . '/whatsapp/callback'
        ];

        if(!empty($this->body_params)){
            $array_data['message']['body_params'] = $this->body_params;
        }
        if(!empty($this->media_params)){
            $array_data['message']['media'] = $this->media_params;
        }
        if(!empty($this->buttons)){
            $array_data['message']['buttons'] = $this->buttons;
        }

        $this->prepared_data = $array_data;

        \Log::debug("Data Prepared >>>>>> " . json_encode($this->prepared_data));

    }

    public function checkParameters(){
        if(empty($this->botId)){
            throw new WhatsappException( "Bot ID Requerido");
        }
        if(empty($this->number)){
            throw new WhatsappException( "Número de Teléfono es requerido");
        }
        // if(empty($this->body_params)){
        //     throw new WhatsappException( "Datos requeridos para el envío");
        // }
    }

    public function authorizationOfSending(){

        \Log::debug("Authorize <<<<<<<<<<< ". $this->number);
        
        $body = [ "numbers" => [ $this->number ]];
        
        \Log::debug("Authorize <<<<<<<<<<< " . json_encode($body));

        try{
            \Log::debug("/whatsapp/v1/$this->botId/provision/optin");
            $response = $this->client->request('POST', "/whatsapp/v1/$this->botId/provision/optin", [ "json" => $body ] );

            \Log::debug("Response Authorization >>>>>>>>>>>>>> ");
            $response = $this->parse_response($response);

            \Log::debug("Response Authorization  " . json_encode($response));

            if( sizeof($response['failedToOptInNumbers']) > 0 ){
                // \Log::debug("No se tiene autorización para el envio del Mensaje envio al numero  : " . $response['failedToOptInNumbers'][0]['msisdn'] . " Razón: " . $response['failedToOptInNumbers'][0]['rejectionReason']);
                return [false, $response['failedToOptInNumbers'][0]];
            }

            return [true, null];


        } catch(RequestException  $ex){
            \Log::debug("Error Connection Authorize ");
            \Log::debug($ex->getResponse()->getBody());

            throw new WhatsappException(  $ex->getResponse()->getBody(), true );
        }

    }

    public function CancelAuthorization(){
        $body = [ "numbers" => [ $this->number ]];

        try{

            $response = $this->client->request('DELETE', "/whatsapp/v1/$this->botId/provision/optin", [ "json" => $body ] );

            $response = $this->parse_response($response);

            return $response;

        } catch( RequestException $ex ){

            throw new WhatsappException( $ex->getResponse()->getBody() );
        }

    }

    public function sendMessage (){
        
        $this->checkParameters();

        $this->prepared_data();

        \Log::debug("Data to sent >>>>>>>>> " . json_encode($this->prepared_data));

        try{

            $response = $this->client->request( 'POST', "/whatsapp/v1/$this->botId/messages", ["json" => $this->prepared_data] );

            $parsed_response = $this->parse_response($response);

            \Log::debug("Response Send Message >>>>>>>>>>>>>> " . json_encode($parsed_response) );

            $this->setResponse($parsed_response);

            return $parsed_response;
        
        } catch(RequestException $ex){

            \Log::debug("Error al enviar el mensaje ". $ex->getResponse()->getBody());

            throw new WhatsappException( $ex->getResponse()->getBody(), true );

        }

    }

}
