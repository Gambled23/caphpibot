<?php

namespace App;


use Discord\Parts\User\Activity;
use Illuminate\Support\Facades\Route;
use Laracord\Laracord;

class Bot extends Laracord
{
    /**
     * The HTTP routes.
     */
    public function routes(): void
    {
        Route::middleware('auth')->group(function () {
            // Route::get('/', fn () => collect($this->registeredCommands)->map(fn ($command) => [
            //     'signature' => $command->getSignature(),
            //     'description' => $command->getDescription(),
            // ]));
        });
    }

    public function afterBoot(): void
    {
        $activity = $this->discord()->factory(Activity::class, [
            'type' => Activity::TYPE_STREAMING,
            'url' => 'https://www.twitch.tv/elcapibe',
            'name' => 'League of Capibara',
        ]);

        $this->discord()->updatePresence($activity);
    }
}
