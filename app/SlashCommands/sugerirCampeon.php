<?php

namespace App\SlashCommands;

use Illuminate\Support\Facades\DB;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Sugerencia;
use Carbon\Carbon;

include 'funciones.php';

class sugerirCampeon extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'sugerir-campeon';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Comandos sobre sugerencias de campeones';

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
      
      $data = $interaction->data;
      $sugerir = $data->options['sugerir'];
      $listado = $data->options['listado'];
      $votar = $data->options['votar'];

      if ($sugerir) {
        $sugerencia = Sugerencia::Create(
          [
            'campeon' => $sugerir->options['campeon']->value,
            'build' => $sugerir->options['build']->value,
            'rol' => $sugerir->options['rol']->value,
            'discord_id' => $interaction->user->id,
          ]
        );
        $interaction->respondWithMessage(
          $this
            ->message()
            ->title('Sugerencia enviada!')
            ->content("Tu sugerencia de {$sugerencia->campeon} en {$sugerencia->rol} ha sido enviada al capibe para su consideración. Gracias!\n\nSi quieres ver el listado de sugerencias y votar por una, usa el comando /sugerir-campeon listado.")
            ->build()
        );
      }
      
      if ($listado) {
        $usuarios = "";
        $builds = "";
        $votos = "";

        $sugerencias = DB::table('sugerencias')
          ->where('jugado', false)
          ->orderBy('votos', 'desc')
          ->get();

        foreach ($sugerencias as $sugerencia) {
            $usuarios .= "{$sugerencia->id} - <@{$sugerencia->discord_id}>\n";
            $builds .= "{$sugerencia->campeon} en {$sugerencia->rol}; {$sugerencia->build}\n";
            $votos .= "{$sugerencia->votos}\n";
        }

        $interaction->respondWithMessage(
            $this->message('Sugerencias de campeones')
            ->field('ID - Usuario', $usuarios)
            ->field('Build', $builds)
            ->field('Votos', $votos)
            ->build(),
        );
      }

      if ($votar) {
        $sugerencia_id = $votar->options['sugerencia']->value;
        $sugerencia = DB::table('sugerencias')
                      ->where('id', $sugerencia_id)
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
        $user = DB::table('users')
          ->where('discord_id', $interaction->user->id)
          ->first();

        if ($user->ultimo_voto === null || Carbon::parse($user->ultimo_voto)->lt(Carbon::now()->subDay())) {
          $now = Carbon::now('America/Chicago');
          DB::table('users')
            ->where('discord_id', $interaction->user->id)
            ->update(['ultimo_voto' => $now]);
          DB::table('sugerencias')
            ->where('id', $sugerencia_id)
            ->increment('votos');
          $sugerencia->votos++;
          $interaction->respondWithMessage(
            $this->message("Voto registrado!")
              ->content("Haz votado por {$sugerencia->campeon} en {$sugerencia->rol}\nVotos actuales: {$sugerencia->votos}\nPodrás volver a votar en 24 horas.\n\nAdemás, haz ganado $25 capicoins por votar.")
              ->build(),
          );
          agregarCapicoins($interaction->user->id, 25);
        } else {
          $hoursLeft = 24 - Carbon::now('America/Chicago')->diffInHours(Carbon::parse($user->ultimo_voto));
          $interaction->respondWithMessage(
            $this->message("Voto prematuro! D:")
              ->content("Aún no puedes votar por otra sugerencia. Vuelve en {$hoursLeft} horas.")
              ->error()
              ->build(),
          );
        }
      }
    }

    public function options()
    {
        $option_sugerir = new Option($this->discord());
        $option_sugerir_campeon = new Option($this->discord());
        $option_sugerir_rol = new Option($this->discord());
        $option_sugerir_build = new Option($this->discord());
        $option_listado = new Option($this->discord());
        $option_votar = new Option($this->discord());
        $option_sugerencia = new Option($this->discord());
                  
        $option_sugerencia
          ->setName('sugerencia')
          ->setDescription('La sugerencia (ID) a la que quieres votar')
          ->setType(Option::INTEGER)
          ->setRequired(true);

        return [
          $option_sugerir
            ->setName('sugerir')
            ->setDescription('Sugerir un nuevo campeón')
            ->setType(Option::SUB_COMMAND)
            ->addOption(
                $option_sugerir_campeon
                  ->setName('campeon')
                  ->setDescription('Nombre del campeón')
                  ->setType(Option::STRING)
                  ->setRequired(true)
            )
            ->addOption(
              $option_sugerir_build
              ->setName('build')
              ->setDescription('Cómo se debería armar el campeon')
              ->setType(Option::STRING)
              ->setRequired(true))
            ->addOption(
                $option_sugerir_rol
                  ->setName('rol')
                  ->setDescription('Rol del campeón')
                  ->setType(Option::STRING)
                  ->setRequired(true)
            ),
          
          $option_listado
            ->setName('listado')
            ->setDescription('Ver los campeones sugeridos')
            ->setType(Option::SUB_COMMAND),

          $option_votar
            ->setName('votar')
            ->setDescription('Vota por un campeón')
            ->setType(Option::SUB_COMMAND)
            ->addOption($option_sugerencia),
        ];
    }
}
