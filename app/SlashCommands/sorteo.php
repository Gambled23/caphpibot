<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;

class sorteo extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'sorteo';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Elige un miembro del servidor al azar.';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Indiciates whether the slash command requires admin permissions.
     *
     * @var bool
     */
    protected $admin = true;

    /**
     * Indicates whether the slash command should be displayed in the commands list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Handle the slash command.
     *
     * @param  \Discord\Parts\Interactions\Interaction  $interaction
     * @return void
     */

public function handle($interaction)
{
    $discord = $this->discord();
    $guild = $discord->guilds->get('id', '1117239638990536834');
    foreach ($guild->members as $member) {
        $members[] = $member;
    }
    $randomMember = $members[array_rand($members)];

    $interaction->respondWithMessage(
        $this
            ->message()
            ->title('Felicidades!!')
            ->content('El ganador del sorteo es: ' . $randomMember)
            ->build()
    );
}
}
