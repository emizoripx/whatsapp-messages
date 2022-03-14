<?php

namespace EmizorIpx\WhatsappMessages\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WhatsappMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "message_key" => $this->message_key,
            "number_phone" => $this->number_phone,
            "authorize_to_sent" => boolval($this->authorize_to_sent),
            "rejection_reason" => $this->rejection_reason,
            "status" => $this->status,
            "state" => $this->state,
            "status_description" => $this->status_description,
            "send_date" => $this->send_date,
            "dispatched_date" => $this->dispatched_date,
            "delivered_date" => $this->delivered_date,
            "read_date" => $this->read_date,
            "errors" => $this->errors,
            "error_details" => $this->error_details,
        ];
    }
}
