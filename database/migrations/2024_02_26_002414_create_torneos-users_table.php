<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('torneos-users', function (Blueprint $table) {
            $table->id();
            $table->integer('discord_id')->unsigned();
            $table->foreign('discord_id')->references('discord_id')->on('users');
            $table->integer('torneo_id')->unsigned();
            $table->foreign('torneo_id')->references('id')->on('torneos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
