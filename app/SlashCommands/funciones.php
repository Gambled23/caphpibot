<?php

use App\Models\User;

if (!function_exists('registrarUsuario')) {
    function registrarUsuario ($interaction) {
        $user = User::firstOrCreate(
            ['discord_id' => $interaction->user->id],
            ['username' => $interaction->user->username, 'is_admin' => false]
        );
    }
}

if (!function_exists('agregarCapicoins')) {
    function agregarCapicoins ($discord_id, $capicoins) {
        $user = User::where('discord_id', $discord_id)->first();
        $user->capicoins += $capicoins;
        $user->save();
    }
}

if (!function_exists('comprobarCapicoinsSuficientes')) {
    function comprobarCapicoinsSuficientes ($discord_id, $capicoins) {
        $user = User::where('discord_id', $discord_id)->first();
        return $user->capicoins >= $capicoins;
    }
}