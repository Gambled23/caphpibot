<?php

namespace App\SlashCommands;

use Illuminate\Support\Facades\DB;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Sugerencia;

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
      $data = $interaction->data;
      $sugerir = $data->options['sugerir'];
      $listado = $data->options['listado'];

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
          ->orderBy('votos', 'desc')
          ->get();

        foreach ($sugerencias as $sugerencia) {
            $usuarios .= "<@{$sugerencia->discord_id}>\n";
            $builds .= "{$sugerencia->campeon} en {$sugerencia->rol}; {$sugerencia->build}\n";
            $votos .= "{$sugerencia->votos}\n";
        }

        $interaction->respondWithMessage(
            $this->message('Participantes del torneo')
            ->field('Usuario que hizo la sugerencia', $usuarios)
            ->field('Build', $builds)
            ->field('Votos', $votos)
            ->build(),
            ephemeral: true
        );
      }
    }

    public function options()
    {
        $option_sugerir = new Option($this->discord());
        $option_sugerir_campeon = new Option($this->discord());
        $option_sugerir_rol = new Option($this->discord());
        $option_sugerir_build = new Option($this->discord());
        $option_listado = new Option($this->discord());

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
    ];
}
}
