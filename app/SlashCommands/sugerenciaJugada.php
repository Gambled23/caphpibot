<?php

namespace App\SlashCommands;

use Illuminate\Support\Facades\DB;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Sugerencia;
use Carbon\Carbon;

include 'funciones.php';

class sugerenciaJugada extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'sugerencia-jugada';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Elimina una sugerencia (Si ya se jugó o no se planea jugar).';

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
        registrarUsuario($interaction);
        
        $sugerencia_id = $interaction->data->options['sugerencia']->value;
        $sugerencia = DB::table('sugerencias')
            ->where('id', $sugerencia_id)
            ->select('discord_id')
            ->first();

        DB::table('sugerencias')
            ->where('id', $sugerencia_id)
            ->update(['jugado' => true]);

        $discord_id = $sugerencia->discord_id;
        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('Sugerencia eliminada')
              ->content("Haz marcado la sugerencia como jugada.\nQuizá sea un buen momento para avisarle a la capibanda que ya jugaste la sugerencia 🤔.\n\nO que fue una babosada de sugerencia.")
              ->build(),
              ephemeral: true
        );
        agregarCapicoins($discord_id, 25);
    }

    public function options(){
        $option = new Option($this->discord());
        $option
            ->setName('sugerencia')
            ->setDescription('La sugerencia que se va a tachar.')
            ->setType(Option::STRING)
            ->setRequired(true);

        $sugerencias = DB::table('sugerencias')
          ->where('jugado', 0)
          ->get();

        foreach ($sugerencias as $sugerencia) {
            $sugerencia_name = "{$sugerencia->campeon}|{$sugerencia->rol}|{$sugerencia->build}";
            if (strlen($sugerencia_name) > 100) {
                $sugerencia_name = substr($sugerencia_name, 0, 100);
            }
            $choice = (new Choice($this->discord()))->setName($sugerencia_name)->setValue((string) $sugerencia->id);
            $option->addChoice($choice);
        }

        return [$option];
    }
}
