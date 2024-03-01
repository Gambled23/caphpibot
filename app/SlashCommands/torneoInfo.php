<?php

namespace App\SlashCommands;

use Laracord\Discord\Message;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use Discord\Parts\Interactions\Interaction;
use App\Models\Torneo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class torneoInfo extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'torneo-info';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Ver información de un torneo';

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

    public function listaParticipantes(Interaction $interaction, $torneo_id)
    {
        $torneo_users = DB::table('torneos_users')
            ->where('torneo_id', $torneo_id)
            ->get();

        $fields = [];
        $usuarios = "";
        $confirmaciones = "";
        foreach ($torneo_users as $index => $user) {
            $usuarios .= "<@{$user->discord_id}>\n";
            $confirmaciones .= $user->confirmado ? "✅\n" : "❌\n";
        }

        $interaction->respondWithMessage(
            $this->message('Participantes del torneo')
            ->field('Usuario', $usuarios)
            ->field('Confirmado', $confirmaciones)
            ->build(),
            ephemeral: true
        );

    }

    /**
     * Handle the slash command.
     *
     * @param  \Discord\Parts\Interactions\Interaction  $interaction
     * @return void
     */
    public function handle($interaction)
    {
        $torneoId = $interaction->data->options['torneo']->value;
        $torneo = Torneo::where('id', $torneoId)->first();
        $interaction->respondWithMessage(
            $this
              ->message()
              ->title($torneo->nombre)
              ->content("Fecha:  {$torneo->fecha} \nDescripción:  {$torneo->descripcion} \nParticipantes totales:  {$torneo->participantes}")
              ->button('Participantes', function(Interaction $interaction) use ($torneo) {
                $this->listaParticipantes($interaction, $torneo->id);
                }, emoji: '🔎')
              ->build()
        );
    }

    public function options()
    {
        $option = new Option($this->discord());
        $option
            ->setName('torneo')
            ->setDescription('Torneo del cual quieres ver información')
            ->setType(Option::STRING)
            ->setRequired(true);

        $antier = Carbon::now('America/Chicago')->subDays(2);
        $torneosRecientes = DB::table('torneos')
            ->where('fecha', '>', $antier)
            ->get();

        foreach ($torneosRecientes as $torneo) {
            $choice = (new Choice($this->discord()))->setName($torneo->nombre)->setValue((string) $torneo->id);
            $option->addChoice($choice);
        }

        return [$option];
    }
}
