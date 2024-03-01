<?php

namespace App\Services;

use Laracord\Services\Service;
use Laracord\Discord\Message;
use App\Models\Torneo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Torneo_hora_antes extends Service
{
    /**
     * The service interval.
     */
    protected int $interval = 3600;
    /**
     * Handle the service.
     */
    public function handle(): void
    {
        $now = Carbon::now('America/Chicago');
        $oneHourFromNow = Carbon::now()->addHour();

        $upcomingTorneos = DB::table('torneos')
            ->whereBetween('fecha', [$now, $oneHourFromNow])
            ->get();

        $channel = $this->discord()->getChannel('1212412059019649086');
        if ($upcomingTorneos->count() > 0) {
            foreach ($upcomingTorneos as $torneo) {
                $this
                    ->message()
                    ->title($torneo->nombre)
                    ->content('El torneo ' . $torneo->nombre . ' empieza en una hora!!')
                    ->send($channel);
            }
        }
    }
}
