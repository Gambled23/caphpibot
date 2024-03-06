<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;

class apostar extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'apostar';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Distintos minijuegos para ganar capicoins';

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
              ->title('apostar')
              ->content('Hello world!')
              ->build()
        );
    }

    public function options()
    {
        $option_capicoins = new Option($this->discord());
        $option_capicoins
            ->setName('capicoins')
            ->setDescription('Cantidad de capicoins a apostar')
            ->setType(Option::INTEGER)
            ->setRequired(true);
        
        $subcommand_flipDaCoin = new Option($this->discord());

        $subcommand_dado = new Option($this->discord());
        $option_ladoDado = new Option($this->discord());
        $option_ladoDado
            ->setName('numero')
            ->setDescription('Numero del 1 al 6 al que apostar')
            ->setType(Option::INTEGER)
            ->setRequired(true);
        for ($i = 1; $i <= 6; $i++) {
            $choice = (new Choice($this->discord()))->setName($i)->setValue($i);
            $option_ladoDado->addChoice($choice);
        }

        return [
            $subcommand_flipDaCoin
                ->setName('flip-da-coin')
                ->setDescription('Lanza una moneda y gana hasta el doble de capicoins!')
                ->setType(Option::SUB_COMMAND)
                ->addOption(
                    $option_capicoins
            ),

            $subcommand_dado
                ->setName('dado')
                ->setDescription('Lanza un dado y gana hasta el triple de capicoins!')
                ->setType(Option::SUB_COMMAND)
                ->addOption(
                    $option_capicoins,
                    $option_ladoDado
            ),
            
        ];
    }
}
