<?php

namespace App\SlashCommands;

use Illuminate\Support\Facades\DB;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Sugerencia;
use App\Models\User;
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
        $user = User::where('discord_id', $interaction->member->user->id)->first();
        $userMoney = $user->capicoins;

        $democracia = $interaction->data->options['democracia'];
        $anarquia = $interaction->data->options['anarquia'];
        $censura = $interaction->data->options['censura'];
        if ($democracia) {
            if (!comprobarCapicoinsSuficientes($interaction->user->id, 300)) {
                $interaction->respondWithMessage(
                    $this->message("Capicoins insuficientes")
                        ->content("No tienes suficientes capicoins para alterar las elecciones")
                        ->error()
                        ->footerText("Capicoins actuales: {$userMoney}")
                        ->build(),
                );
                return;
            }
            $democracia_sugerencia = $democracia->options['sugerencia']->value;
            $sugerencia = DB::table('sugerencias')
                          ->where('id', $democracia_sugerencia)
                          ->first();
            if (!$sugerencia) {
              $interaction->respondWithMessage(
                $this->message("Sugerencia no existente")
                  ->content("La ID que metiste no existe en las sugerencias, usa /sugereir-campeon listado para ver las sugerencias actuales.")
                  ->error()
                  ->build(),
              );
              return;
            }
            if ($sugerencia->jugado) {
              $interaction->respondWithMessage(
                $this->message("Sugerencia ya jugada")
                  ->content("La ID que metiste pertenece a una sugerencia ya jugada, usa /sugereir-campeon listado para ver las sugerencias actuales.")
                  ->error()
                  ->build(),
              );
              return;
            }
            DB::table('sugerencias')
              ->where('id', $democracia_sugerencia)
              ->increment('votos');
            agregarCapicoins($interaction->user->id, -300);
            $interaction->respondWithMessage(
                $this
                  ->message()
                  ->title('Elecciones compradas!')
                  ->content("Acabas de comprar un voto extra para la sugerencia de {$sugerencia->campeon} en {$sugerencia->rol}. Gracias por ser un capibito ejemplar.")
                  ->footerText("Capicoins actuales: {$userMoney}")
                  ->build()
            );
        }
        if ($anarquia) {
            if (!comprobarCapicoinsSuficientes($interaction->user->id, 250)) {
                $interaction->respondWithMessage(
                    $this->message("Capicoins insuficientes")
                        ->content("No tienes suficientes capicoins para alterar las elecciones")
                        ->error()
                        ->footerText("Capicoins actuales: {$userMoney}")
                        ->build(),
                );
                return;
            }
            $anarquia_sugerencia = $anarquia->options['sugerencia']->value;
            $sugerencia = DB::table('sugerencias')
                          ->where('id', $anarquia_sugerencia)
                          ->first();
            if (!$sugerencia) {
              $interaction->respondWithMessage(
                $this->message("Sugerencia no existente")
                  ->content("La ID que metiste no existe en las sugerencias, usa /sugereir-campeon listado para ver las sugerencias actuales.")
                  ->error()
                  ->build(),
              );
              return;
            }
            if ($sugerencia->jugado) {
              $interaction->respondWithMessage(
                $this->message("Sugerencia ya jugada")
                  ->content("La ID que metiste pertenece a una sugerencia ya jugada, usa /sugereir-campeon listado para ver las sugerencias actuales.")
                  ->error()
                  ->build(),
              );
              return;
            }
            DB::table('sugerencias')
              ->where('id', $anarquia_sugerencia)
              ->decrement('votos');
            agregarCapicoins($interaction->user->id, -250);
            $interaction->respondWithMessage(
                $this
                  ->message()
                  ->title('Elecciones compradas!')
                  ->content("Acabas de eliminar un voto para la sugerencia de {$sugerencia->campeon} en {$sugerencia->rol}. Gracias por ser un capibito corrupto.")
                  ->footerText("Capicoins actuales: {$userMoney}")
                  ->build()
            );
        }
        if ($censura) {
            $censura_usuario = $censura->options['usuario']->value;
            if (!comprobarCapicoinsSuficientes($interaction->user->id, 675)) {
                $interaction->respondWithMessage(
                    $this->message("Capicoins insuficientes")
                        ->content("No tienes suficientes capicoins para censurar la libertad de expresión.")
                        ->error()
                        ->footerText("Capicoins actuales: {$userMoney}")
                        ->build(),
                );
                return;
            }
            $guild = $this->discord()->guilds->get('id', $interaction->guild_id);
            $role = $guild->roles->get('name', 'censurado');
            $member = $guild->members->get('id', $censura_usuario);

            $member->addRole($role);
            $interaction->respondWithMessage(
                $this
                  ->message()
                  ->title('Censura comprada!')
                  ->content("Acabas de censurar a <@{$censura_usuario}> por una hora. Gracias por apoyar la libertad de expresión.")
                  ->footerText("Capicoins actuales: {$userMoney}")
                  ->build()
            );
            agregarCapicoins($interaction->user->id, -675);

        }
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
