<?php

namespace App\Services;

use Laracord\Services\Service;
use Laracord\Discord\Message;
use Discord\Parts\Interactions\Interaction;
use App\Models\Torneo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Torneo_dia_antes extends Service
{
    /**
     * The service interval.
     */
    protected int $interval = 86400;
    /**
     * Handle the service.
     */

    public function confirmInteraction(Interaction $interaction, $torneo_id)
    {
        $interaction->respondWithMessage(
            $this->message('Haz confirmado tu asistencia')->build(),
            ephemeral: true
        );
        $torneo_user = DB::table('torneos_users')
            ->where('discord_id', $interaction->user->id)
            ->where('torneo_id', $torneo_id)
            ->first();
        DB::table('torneos_users')
            ->where('id', $torneo_user->id)
            ->update(['confirmado' => true]);
    }

    public function handle(): void
    {
        $now = Carbon::now('America/Chicago');
        $oneDayFromNow = Carbon::now('America/Chicago')->addDay();

        $upcomingTorneos = DB::table('torneos')
            ->whereBetween('fecha', [$now, $oneDayFromNow])
            ->get();

        $channel = $this->discord()->getChannel('1199887896488980593');
        if ($upcomingTorneos->count() > 0) {
            foreach ($upcomingTorneos as $torneo) {
                $this
                    ->message()
                    ->title($torneo->nombre)
                    ->content("El torneo {$torneo->nombre} empieza en 24 horas\n Ultima oportunidad para inscribirse con /inscribirtorneo")
                    ->send($channel);
                
                    //Avisar a los participantes
                    $torneos_users = DB::table('torneos_users')
                        ->where('torneo_id', $torneo->id)
                        ->get();
                    foreach ($torneos_users as $torneo_user) {
                        $user = $this->discord()->users->get('id', $torneo_user->discord_id);
                        $this
                            ->message()
                            ->title($torneo->nombre)
                            ->content("El torneo {$torneo->nombre} empieza en 24 horas\n Confirma tu asistencia para poder participar")
                            ->button('Confirmar', function(Interaction $interaction) use ($torneo) {
                                $this->confirmInteraction($interaction, $torneo->id);
                            }, emoji: '✅')
                            ->sendTo($user);
                    }
            }
        }
    }
}
