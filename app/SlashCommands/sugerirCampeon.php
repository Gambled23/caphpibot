<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;

class sugerirCampeon extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'sugerir-campeon';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Comandos sobre sugerencias de campeones';

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
              ->title('sugerirCampeones')
              ->content('Hello world!')
              ->build()
        );
    }

    public function options()
{
    $option = new Option($this->discord());
    $option2 = new Option($this->discord());
    
    return [
        $option
          ->setName('sugerir')
          ->setDescription('Sugerir un nuevo campeón')
          ->setType(Option::SUB_COMMAND),
        $option2
          ->setName('listado')
          ->setDescription('Ver los campeones sugeridos')
          ->setType(Option::SUB_COMMAND),
    ];
}
}
