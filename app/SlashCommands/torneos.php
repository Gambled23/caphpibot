<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;

class torneos extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'torneos';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Comandos para torneos.';

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
    protected $admin = false;

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
        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('torneos')
              ->content('Hello world!')
              ->build()
        );
    }
}
