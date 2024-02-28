<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use App\Models\Torneo;

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
    protected $description = 'Crear un nuevo torneo';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [
        [
            'name' => 'nombre',
            'description' => 'Nombre del torneo',
            'type' => Option::STRING,
            'required' => true,
        ],
        [
            'name' => 'dia',
            'description' => 'Día del torneo',
            'type' => Option::STRING,
            'required' => true,
        ],
        [
            'name' => 'mes',
            'description' => 'Mes del torneo',
            'type' => Option::STRING,
            'required' => true,
        ],
        [
            'name' => 'hora',
            'description' => 'Hora del centro de méxico (formato 24h)',
            'type' => Option::STRING,
            'required' => true,
        ],
        [
            'name' => 'descripcion',
            'description' => 'Descripción del torneo',
            'type' => Option::STRING,
            'required' => false,
        ],
    ];

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
        $options = $interaction->data->options;
        $this->console()->log($options['nombre']->value);
        $this->console()->log($options['dia']->value);
        $this->console()->log($options['mes']->value);
        $this->console()->log($options['hora']->value);
        
        $torneo = Torneo::create([
            'nombre' => $options['nombre']->value,
            'descripcion' => isset($options['descripcion']) ? $options['descripcion']->value : 'uwu',
            'dia' => $options['dia']->value,
            'mes' => $options['mes']->value,
            'hora' => $options['hora']->value,
        ]);

        $interaction->respondWithMessage(
            $this
              ->message()
              ->title("{$torneo->nombre}")
              ->content("{$torneo->descripcion} \nFecha: {$torneo->dia} de {$torneo->mes} a las {$torneo->hora}")
              ->build());
    }
}
