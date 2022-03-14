<?php

namespace EmizorIpx\WhatsappMessages\Utils;

use Carbon\Carbon;

class WhatsappMessageStates{

    const QUEUED = 'queued';
    const DISPATCHED = 'dispatched';
    const SENT = 'sent';
    const DELIVERED = 'delivered';
    const READ = 'read';
    const DELETED = 'deleted';
    const FAILED = 'failed';
    const NO_OPT_IN = 'no_opt_in';
    const NO_CAPABILITY = 'no_capability';
    


    public static function getDescriptionState($state){ 
        switch ($state) {
            case  static::QUEUED:
                return "El mensaje fue procesado correctamente para enviar.";
                break;
            case  static::DISPATCHED:
                return "El mensaje fue enviado correctamente.";
                break;
            case  static::SENT:
                return "El mensaje fue enviado correctamente al usuario final.";
                break;
            case  static::DELIVERED:
                return "El mensaje fue entregado con éxito al usuario final.";
                break;
            case  static::READ:
                return "El mensaje ha sido leído por el usuario final.";
                break;
            case  static::DELETED:
                return "El mensaje ha sido borrado.";
                break;
            case  static::NO_OPT_IN:
                return "Mensaje rechazado, el destinatario no esta registrado.";
                break;
            case  static::FAILED:
                return "No se ha podido entregar el mensaje";
                break;
            case  static::NO_CAPABILITY:
                return "Mensaje rechazado, el destinatario no tiene capacidad para Whatsapp.";
                break;
            
            default:
                return "Estado no esperado.";
                break;
        }
    }

    public static function setStateDate($state, $date = null){

        $array_data = [];

        switch ($state) {
            case static::DISPATCHED:
                return ['dispatched_date' => Carbon::parse($date)->toDateTimeString()];
                break;
            case static::SENT:
                return ['send_date' => Carbon::parse($date)->toDateTimeString()];
                break;
            case static::DELIVERED:
                return ['delivered_date' => Carbon::parse($date)->toDateTimeString()];
                break;
            case static::READ:
                return ['read_date' => Carbon::parse($date)->toDateTimeString()];
                break;
            
            default:
                return $array_data;
                break;
        }

    }

}