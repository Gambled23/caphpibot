<?php

namespace App\SlashCommands;

use Illuminate\Support\Facades\DB;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Sugerencia;
use Carbon\Carbon;

include 'funciones.php';

class storecomprar extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'store-comprar';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Comprar un producto de la tienda';

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
              ->title('storecomprar')
              ->content('Hello world!')
              ->build()
        );
    }

    /**
     * Get the command options.
     *
     * @return array
     */
    public function options()
    {
        $democracia = new Option($this->discord());
        $democracia_sugerrencia = new Option($this->discord());
        $anarquia = new Option($this->discord());
        $anarquia_sugerencia = new Option($this->discord());
        $censura = new Option($this->discord());
        $censura_usuario = new Option($this->discord());
        
        return [
            $democracia
            ->setName('democracia')
            ->setDescription('Compra un voto extra para un campeón sugerido')
            ->setType(Option::SUB_COMMAND)
            ->addOption(
                $democracia_sugerrencia
                  ->setName('sugerencia')
                  ->setDescription('ID de la sugerencia a la que quieres añadirle un voto')
                  ->setType(Option::INTEGER)
                  ->setRequired(true)
            ),

            $anarquia
            ->setName('anarquia')
            ->setDescription('Quitale un voto a un campeón sugerido')
            ->setType(Option::SUB_COMMAND)
            ->addOption(
                $anarquia_sugerencia
                  ->setName('sugerencia')
                  ->setDescription('ID de la sugerencia a la que quieres quitarle un voto')
                  ->setType(Option::INTEGER)
                  ->setRequired(true)
            ),

            $censura
            ->setName('censura')
            ->setDescription('Silencia un miembro (incluso al capibe) durante una hora')
            ->setType(Option::SUB_COMMAND)
            ->addOption(
                $censura_usuario
                  ->setName('usuario')
                  ->setDescription('Usuario al que quieres silenciar')
                  ->setType(Option::MENTIONABLE)
                  ->setRequired(true)
            ),
            
        ];
    }
}
