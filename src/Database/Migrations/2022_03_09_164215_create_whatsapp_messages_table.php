<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('message_key');
            $table->string('message_id')->nullable();
            $table->text('message')->nullable();
            $table->string('number_phone')->nullable();
            $table->boolean('authorize_to_sent')->nullable()->default(true);
            $table->string('rejection_reason')->nullable();
            $table->string('status')->nullable();
            $table->string('state')->nullable();
            $table->string('status_description')->nullable();
            $table->dateTime('send_date')->nullable();
            $table->dateTime('delivered_date')->nullable();
            $table->dateTime('read_date')->nullable();
            $table->dateTime('dispatched_date')->nullable();
            $table->text('errors')->nullable();
            $table->text('error_details')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fel_whatsapp_messages');
    }
}
