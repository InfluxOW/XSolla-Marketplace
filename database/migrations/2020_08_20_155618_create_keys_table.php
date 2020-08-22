<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('distributor_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->index()->constrained('users')->cascadeOnDelete();
            $table->string('serial_number', 30);
            $table->unique(['distributor_id', 'serial_number']);
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
        Schema::dropIfExists('keys');
    }
}
