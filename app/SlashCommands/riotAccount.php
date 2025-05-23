<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\User;

include 'funciones.php';

class RiotAccount extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'capibaccount';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Comandos para gestionar tu capibaccount del servidor.';

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
        $registrar = $data->options['registrar'];
        $info = $data->options['info'];
        if ($registrar) {        
            $user = User::updateOrCreate(
                ['discord_id' => $interaction->user->id],
                [
                    'username' => $interaction->user->username,
                    'riot_id' => "{$registrar->options['username']->value}#{$registrar->options['tagline']->value}",
                    'region' => $registrar->options['region']->value ?? 'lan',
                ]
            );

            $interaction->respondWithMessage(
                $this
                  ->message()
                  ->title('Registro de Riot account')
                  ->content("Tu cuenta {$user->riot_id} ha sido registrada con éxito.")
                  ->build(),
                  ephemeral: true
            );
        }
        else if ($info) {
            $discord_user_id = $info->options['usuario']->value;
            $user = User::where('discord_id', $discord_user_id)->first();
            if ($user->riot_id) {
                $parts = explode('#', $user->riot_id);

                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title("Cuenta de {$user->username}")
                      ->content("
                      https://www.op.gg/summoners/{$user->region}/{$parts[0]}-{$parts[1]}")
                      ->footerText("Capicoins: $" . ($user->capicoins))
                      ->build()
                );
            }
            else
            {
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title("Cuenta de {$user->username}")
                      ->content("{$user->username} aún no ha registrado su cuenta de Riot en el servidor.\n{$user->username} debe utilizar el comando\n/capibaccount registrar <datos>\npara registrarla. ¡Avisale!")
                      ->footerText("Capicoins: $" . ($user->capicoins))
                      ->warning()
                      ->build()
                );
            }
        }
    }

    public function options(){
        return [
            new Option($this->discord(), [
                'name' => 'registrar',
                'description' => 'Registra tu cuenta de League of Legends',
                'type' => Option::SUB_COMMAND,
                'options' => [
                    new Option($this->discord(), [
                        'name' => 'username',
                        'description' => 'Ejemplo: elcapibe',
                        'type' => Option::STRING,
                        'required' => true,
                    ]),
                    new Option($this->discord(), [
                        'name' => 'tagline',
                        'description' => 'Ejemplo: 0429',
                        'type' => Option::STRING,
                        'required' => true,
                    ]),
                    new Option($this->discord(), [
                        'name' => 'region',
                        'description' => 'Establecer si es diferente a LAN',
                        'type' => Option::STRING,
                        'required' => true,
                        'choices' => [
                            ['name' => 'LAN', 'value' => 'lan'],
                            ['name' => 'LAS', 'value' => 'las'],
                            ['name' => 'NA', 'value' => 'na'],
                            ['name' => 'BR', 'value' => 'br'],
                            ['name' => 'EUNE', 'value' => 'eune'],
                            ['name' => 'EUW', 'value' => 'euw'],
                            ['name' => 'OCE', 'value' => 'oce'],
                            ['name' => 'RU', 'value' => 'ru'],
                            ['name' => 'TR', 'value' => 'tr'],
                            ['name' => 'JP', 'value' => 'jp'],
                            ['name' => 'KR', 'value' => 'kr'],
                        ],
                    ]),
                ],
            ]),
            
            new Option($this->discord(), [
                'name' => 'info',
                'description' => 'Información sobre una cuenta de League of Legends',
                'type' => Option::SUB_COMMAND,
                'options' => [
                    new Option($this->discord(), [
                        'name' => 'usuario',
                        'description' => 'Usuario del que quieres ver la cuenta',
                        'type' => Option::USER,
                        'required' => true,
                    ]),
                ],
            ]),
        ];
    }
}
