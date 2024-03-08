<?php

namespace App\SlashCommands;

use Illuminate\Support\Facades\DB;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Sugerencia;
use Carbon\Carbon;

include 'funciones.php';

class store extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'store';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Gasta tus capicoins en distintos articulos.';

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
        registrarUsuario($interaction);
        
        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('store')
              ->content('Hello world!')
              ->build()
        );
    }

    /**
     * Handle the slash command options.
     *
     * @param  \Discord\Parts\Interactions\Interaction  $interaction
     * @return void
     */
    public function options()
    {
        return [];
    }
}
