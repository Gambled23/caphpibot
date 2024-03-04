<?php

namespace App\Commands;

use Laracord\Commands\Command;

class actualizar extends Command
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'actualizar';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el bot.';

    /**
     * Determines whether the command requires admin permissions.
     *
     * @var bool
     */
    protected $admin = true;

    /**
     * Determines whether the command should be displayed in the commands list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Handle the command.
     *
     * @param  \Discord\Parts\Channel\Message  $message
     * @param  array  $args
     * @return void
     */
    public function handle($message, $args)
    {
        dd('actualizar');
    }
}
