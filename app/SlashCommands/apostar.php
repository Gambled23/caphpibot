<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\User;
include 'funciones.php';

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
        registrarUsuario($interaction);
        $coin = $interaction->data->options['flip-da-coin'];
        $dado = $interaction->data->options['dado'];
        $user = User::where('discord_id', $interaction->member->user->id)->first();
        $userMoney = $user->capicoins;

        if ($coin) {
            $capicoins = $coin->options['capicoins']->value;
            if ($capicoins > $userMoney) {
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title('Capicoins insuficientes')
                      ->content("No tienes suficientes capicoins para apostar esa cantidad")
                      ->footerText("Capicoins actuales: {$userMoney}")
                      ->error()
                      ->build()
                );
                return;
            }

            $ladoMoneda = $coin->options['cara']->value;
            $numrand = rand(1, 2);
            if ($ladoMoneda == $numrand) {
                $ganancia = $capicoins * .75;
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title('Ganador 🪙🎉')
                      ->content("Has ganado {$ganancia} capicoins")
                      ->footerText("Capicoins actuales: $" . ($userMoney + $ganancia))
                      ->build()
                );
            } else {
                $ganancia = -1 * $capicoins;
                if ($numrand == 1) {
                    $numrand = 'cara';
                } elseif ($numrand == 2){
                    $numrand = 'cruz';
                }
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title('Perdedor :c')
                      ->content("Ha salido {$numrand}, mejor suerte la próxima vez!")
                      ->footerText("Capicoins actuales: $" . ($userMoney + $ganancia))
                      ->warning()
                      ->build()
                );
            }
        } 
        if ($dado) {
            $capicoins = $dado->options['capicoins']->value;
            if ($capicoins > $userMoney) {
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title('Capicoins insuficientes')
                      ->content("No tienes suficientes capicoins para apostar esa cantidad")
                      ->footerText("Capicoins actuales: {$userMoney}")
                      ->error()
                      ->build()
                );
                return;
            }
            $ladoDado = $dado->options['numero']->value;
            $numrand = rand(1, 6);
            if ($ladoDado == $numrand) {
                $ganancia = $capicoins * 3;
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title('Ganador 🪙🎉')
                      ->content("Has ganado {$ganancia} capicoins")
                      ->footerText("Capicoins actuales: $" . ($userMoney + $ganancia))
                      ->build()
                );
            } else {
                $ganancia = -1 * $capicoins;
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title('Perdedor :c')
                      ->content("Ha salido el número {$numrand}, mejor suerte la próxima vez!")
                      ->footerText("Capicoins actuales: $" . ($userMoney + $ganancia))
                      ->warning()
                      ->build()
                );
            }
        }
        agregarCapicoins($interaction->user->id, $ganancia);
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
        $option_ladoMoneda = new Option($this->discord());
        $option_ladoMoneda
            ->setName('cara')
            ->setDescription('¿A cual cara de la moneda le quieres apostar?')
            ->setType(Option::INTEGER)
            ->setRequired(true);
        $cara = (new Choice($this->discord()))->setName('Cara')->setValue(1);
        $option_ladoMoneda->addChoice($cara);
        $cruz = (new Choice($this->discord()))->setName('Cruz')->setValue(2);
        $option_ladoMoneda->addChoice($cruz);

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
                ->addOption($option_capicoins)
                ->addOption($option_ladoMoneda),

            $subcommand_dado
                ->setName('dado')
                ->setDescription('Lanza un dado y gana hasta el triple de capicoins!')
                ->setType(Option::SUB_COMMAND)
                ->addOption($option_capicoins)
                ->addOption($option_ladoDado),
            
        ];
    }
}
