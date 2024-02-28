<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
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

    public function options()
    {
        $option_nombre = new Option($this->discord());
        $option_dia = new Option($this->discord());
        $option_mes = new Option($this->discord());
        $choice_mes_enero = (new Choice($this->discord()))->setName('Enero')->setValue('enero');
        $choice_mes_febrero = (new Choice($this->discord()))->setName('Febrero')->setValue('febrero');
        $choice_mes_marzo = (new Choice($this->discord()))->setName('Marzo')->setValue('marzo');
        $choice_mes_abril = (new Choice($this->discord()))->setName('Abril')->setValue('abril');
        $choice_mes_mayo = (new Choice($this->discord()))->setName('Mayo')->setValue('mayo');
        $choice_mes_junio = (new Choice($this->discord()))->setName('Junio')->setValue('junio');
        $choice_mes_julio = (new Choice($this->discord()))->setName('Julio')->setValue('julio');
        $choice_mes_agosto = (new Choice($this->discord()))->setName('Agosto')->setValue('agosto');
        $choice_mes_septiembre = (new Choice($this->discord()))->setName('Septiembre')->setValue('septiembre');
        $choice_mes_octubre = (new Choice($this->discord()))->setName('Octubre')->setValue('octubre');
        $choice_mes_noviembre = (new Choice($this->discord()))->setName('Noviembre')->setValue('noviembre');
        $choice_mes_diciembre = (new Choice($this->discord()))->setName('Diciembre')->setValue('diciembre');
        $option_hora = new Option($this->discord());
        $option_descripcion = new Option($this->discord());

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
                ->setRequired(true)
                ->addChoice($choice_mes_enero)
                ->addChoice($choice_mes_febrero)
                ->addChoice($choice_mes_marzo)
                ->addChoice($choice_mes_abril)
                ->addChoice($choice_mes_mayo)
                ->addChoice($choice_mes_junio)
                ->addChoice($choice_mes_julio)
                ->addChoice($choice_mes_agosto)
                ->addChoice($choice_mes_septiembre)
                ->addChoice($choice_mes_octubre)
                ->addChoice($choice_mes_noviembre)
                ->addChoice($choice_mes_diciembre),
                
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
