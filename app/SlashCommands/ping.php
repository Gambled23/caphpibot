<?php

namespace App\SlashCommands;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use Laracord\Commands\SlashCommand;

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
    protected $description = 'The ping slush command.';

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
        $respuesta = $interaction->data->options['respuesta']->value;

        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('ping')
              ->content('You chose: ' . $respuesta)
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
                ->addChoice($choice1)
                ->addChoice($choice2)
                ->addChoice($choice3)
                ->addChoice($choice4)
                ->addChoice($choice5)
        ];
    }
}
