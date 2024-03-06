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