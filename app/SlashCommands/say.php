<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;

class Say extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'message';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Envía un mensaje a través del bot';

    /**
     * The command options.
     *
     * @var array
     */
    protected $options = [
        [
            'name' => 'message',
            'description' => 'Send a message through the bot.',
            'type' => Option::STRING,
            'required' => true,
        ],
    ];

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
        $message = $data->options['message']->value;
        
        $interaction->respondWithMessage(
            $this
              ->message()
              ->title('Example')
              ->content($message)
              ->build()
        );
    }
}
