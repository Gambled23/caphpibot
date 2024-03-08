<?php

namespace App\SlashCommands;

use Laracord\Discord\Message;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Torneo;
use Carbon\Carbon;

include 'funciones.php';

class ping extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'ping';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'esteesuncomandopruebaquenosirveparanadamásqueparallevarlacuentadecuántasveceslahecagado: 8';

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
        $respuesta = $interaction->data->options['respuesta']->value;
        
        $now = Carbon::now('America/Chicago');
        $interaction->respondWithMessage(
            $this
              ->message()
              ->title("{$respuesta}")
              ->content("También lo uso para probar cosas\ncomo imprimir el ahora {$now}\nmientras vivo en el ayer\ny el mañana no existe")
              ->button('Hello', fn (Interaction $interaction) => $interaction->respondWithMessage(
                $this->message('Well hello to you!AYUDA')->build(),
                ephemeral: true
                ), emoji: '👋')
              ->build()
        );
    }

    public function options()
    {
        $option = new Option($this->discord());
        $choice1 = new Choice($this->discord());
        $choice1->setName('Pato')->setValue('pato');
        $choice2 = new Choice($this->discord());
        $choice2->setName('Peto')->setValue('peto');
        $choice3 = new Choice($this->discord());
        $choice3->setName('Pito')->setValue('pito');
        $choice4 = new Choice($this->discord());
        $choice4->setName('Poto')->setValue('poto');
        $choice5 = new Choice($this->discord());
        $choice5->setName('Xavier')->setValue('xavier');

        return [
            $option
                ->setName('respuesta')
                ->setDescription('la respuesta que dará el bot al comando /ping')
                ->setType(Option::STRING)
                ->setRequired(true)
                ->addChoice($choice1)
                ->addChoice($choice2)
                ->addChoice($choice3)
                ->addChoice($choice4)
                ->addChoice($choice5)
        ];
    }
}
