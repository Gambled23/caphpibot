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
        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('Inscrito al torneo!')
              ->content('Te has inscrito al torneo correctamente.')
              ->build(),
              ephemeral: true
        );
    }

    public function options()
    {
        $option = new Option($this->discord());
        $option
            ->setName('Torneo')
            ->setDescription('El torneo al que te inscribirás.')
            ->setType(Option::STRING)
            ->setRequired(true);

        $now = Carbon::now();
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
