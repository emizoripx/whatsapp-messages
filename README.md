# WHATSAPP MESSAGES PACKAGE v1.0.0

## Package for sending messages via WhatsApp using the SINCH service.
### Supports
- Send messages with a predefined template.
- Send documents and multimedia.
- Send buttons or links.
- Currently supports sending to a single cell phone number.

## Installation
- Para instalar ejecutar el comando
    `composer require emizoripx/whatsapp-messages`

## Configure
Before use, you must configure the following parameters

- In the `.env` file of the project copy and set the following environment variables

```
    WHATSAPP_HOST=
    WHATSAPP_TOKEN=
    WHATSAPP_BOT_ID=
    WHATSAPP_TEMPLATE_NAME=
```

## Usage

- To send a message just call the Facade  `EmizorIpx\WhatsappMessages\Facades\WhatsappMessage` to method `sendWhithTemplate` and specify the required parameters

```php
    
    use EmizorIpx\WhatsappMessages\Facades\WhatsappMessage;
    ...

    WhatsappMessage::sendWhithTemplate( $phone_number, $template_name, $body_params, $media_params, $buttons_params );

```

- Parameters
    - `phone_number` Phone number.
    - `template_name` Template name.
    - `body_params` an array with all the parameters required for the template (they must be in the order required).
    - `media_params` us array where the following parameters must be specified
    ```php
        [
            "type" => "document", //Or image
            "url" => $url_pdf,
            "filename" => "Factura34.pdf" ,
        ]
    ```
    - `buttons_params` us array where the following parameters must be specified
    ```php
        [
            "type" => "url",
            "parameter" => $path,
        ]
    ```

- For response returns an array with a message key and a message, with the message key you can identify in the message that was sent.
    ```php
        [
            "message_key" => "121323EW123YU",
            "message" => "",
        ]
    ```