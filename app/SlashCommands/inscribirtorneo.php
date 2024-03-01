<?php

namespace App\SlashCommands;

use Laracord\Discord\Message;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Torneo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class inscribirtorneo extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'inscribirtorneo';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Inscribirte a un torneo del servidor.';

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
        $usuario = DB::table('users')
        ->where('discord_id', $interaction->user->id)
        ->first();

        if ($usuario) {
            $inscrito = DB::table('torneos_users')
                ->where('discord_id', $interaction->user->id)
                ->where('torneo_id', $interaction->data->options['torneo']->value)
                ->first();
            if ($inscrito) {
                $interaction->respondWithMessage(
                    $this
                    ->message()
                    ->title('Ya inscrito!! ')
                    ->content('Ya te habías inscrito a este torneo previamente.')
                    ->error()
                    ->build(),
                    ephemeral: true
                );
            }
            else {
                DB::table('torneos_users')->insert([
                    'discord_id' => $interaction->user->id,
                    'torneo_id' => $interaction->data->options['torneo']->value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('torneos')
                    ->where('id', $interaction->data->options['torneo']->value)
                    ->increment('participantes');

                $interaction->respondWithMessage(
                    $this
                    ->message()
                    ->title('Inscrito al torneo!')
                    ->content('Te has inscrito al torneo correctamente.')
                    ->build(),
                    ephemeral: true
                );
        }
        }
        else {
            $interaction->respondWithMessage(
                $this
                ->message()
                ->title('Usuario no registrado :(')
                ->content("Para registrarte a un torneo primero debes de registrar tu cuenta de Riot en el servidor.\nUtiliza el comando\n/riot-account register <nombre de invocador>\npara registrarte.")
                ->error()
                ->build(),
                ephemeral: true
            );
        }
    }

    public function options()
    {
        $option = new Option($this->discord());
        $option
            ->setName('torneo')
            ->setDescription('El torneo al que te inscribirás.')
            ->setType(Option::STRING)
            ->setRequired(true);

        $now = Carbon::now('America/Chicago');
        $upcomingTorneos = DB::table('torneos')
            ->where('fecha', '>', $now)
            ->get();

        foreach ($upcomingTorneos as $torneo) {
            $choice = (new Choice($this->discord()))->setName($torneo->nombre)->setValue((string) $torneo->id);
            $option->addChoice($choice);
        }

        return [$option];
    }
}
