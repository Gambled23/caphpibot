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
    protected $name = 'storecomprar';

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
        return [
            new Option($this->discord(), [
                'name' => 'democracia',
                'description' => 'Compra un voto extra para un campeón sugerido',
                'type' => Option::SUB_COMMAND,
                'options' => [
                    new Option($this->discord(), [
                        'name' => 'sugerencia',
                        'description' => 'ID de la sugerencia a la que quieres añadirle un voto',
                        'type' => Option::INTEGER,
                        'required' => true,
                    ]),
                ],
            ]),
            new Option($this->discord(), [
                'name' => 'anarquia',
                'description' => 'Quitale un voto a un campeón sugerido',
                'type' => Option::SUB_COMMAND,
                'options' => [
                    new Option($this->discord(), [
                        'name' => 'sugerencia',
                        'description' => 'ID de la sugerencia a la que quieres quitarle un voto',
                        'type' => Option::INTEGER,
                        'required' => true,
                    ]),
                ],
            ]),
            new Option($this->discord(), [
                'name' => 'censura',
                'description' => 'Silencia un miembro (incluso al capibe) durante una hora',
                'type' => Option::SUB_COMMAND,
                'options' => [
                    new Option($this->discord(), [
                        'name' => 'usuario',
                        'description' => 'Usuario al que quieres silenciar',
                        'type' => Option::MENTIONABLE,
                        'required' => true,
                    ]),
                ],
            ]),
        ];
    }
}
