<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Torneo;
use Carbon\Carbon;

include 'registrarUsuario.php';

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
    protected $hidden = true;

    /**
     * Handle the slash command.
     *
     * @param  \Discord\Parts\Interactions\Interaction  $interaction
     * @return void
     */
    public function handle($interaction)
    {
        registrarUsuario($interaction);
        
        $options = $interaction->data->options;

        $months = [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12,
        ];
        $monthNumber = $months[strtolower($options['mes']->value)] ?? null;        
        $fecha = Carbon::createFromFormat('d m H:i', $options['dia']->value . ' ' . $monthNumber . ' ' . $options['hora']->value);
        
        $torneo = Torneo::create([
            'nombre' => $options['nombre']->value,
            'descripcion' => isset($options['descripcion']) ? $options['descripcion']->value : 'uwu',
            'fecha' => $fecha,
        ]);

        $interaction->respondWithMessage(
            $this
              ->message()
              ->title("{$torneo->nombre}")
              ->content("{$torneo->descripcion} \nFecha: {$torneo->fecha}")
              ->build());
        sleep(10);
        dd('esta no es la mejor manera de actualizar los application commands jej');
    }

    public function options()
    {
        $option_nombre = new Option($this->discord());
        $option_dia = new Option($this->discord());
        $option_mes = new Option($this->discord());
        $option_hora = new Option($this->discord());
        $option_descripcion = new Option($this->discord());

        $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        foreach ($months as $month) {
            $choice = (new Choice($this->discord()))->setName($month)->setValue(strtolower($month));
            $option_mes->addChoice($choice);
        }

        return [
            $option_nombre
                ->setName('nombre')
                ->setDescription('Nombre del torneo')
                ->setType(Option::STRING)
                ->setRequired(true),

            $option_dia
                ->setName('dia')
                ->setDescription('Día del torneo')
                ->setType(Option::STRING)
                ->setRequired(true),

            $option_mes
                ->setName('mes')
                ->setDescription('Mes del torneo')
                ->setType(Option::STRING)
                ->setRequired(true),

            $option_hora
                ->setName('hora')
                ->setDescription('Hora del centro de méxico (formato 24h)')
                ->setType(Option::STRING)
                ->setRequired(true),

            $option_descripcion
                ->setName('descripcion')
                ->setDescription('Descripción del torneo')
                ->setType(Option::STRING)
                ->setRequired(false),
        ];
    }
}
