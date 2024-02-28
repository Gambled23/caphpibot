<?php

namespace App\SlashCommands;

use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\User;

class RiotAccount extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'riot-account';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Vincula tu cuenta de League of Legends con tu cuenta de Discord.';

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
        $registrar = $data->options['registrar'];
        $info = $data->options['info'];
        if ($registrar) {
            // Si la tag empieza con 0 (elcapibe#0429)
            if (strlen($registrar->options['tagline']->value) == 3) { 
                $riot_id = $registrar->options['username']->value . '#0' . $registrar->options['tagline']->value;
            }
            else{
                $riot_id = $registrar->options['username']->value . '#' . $registrar->options['tagline']->value;
            }

            
            //Crear usuario
            $user = User::firstOrCreate(
                ['discord_id' => $interaction->user->id],
                [
                    'username' => $interaction->user->username,
                    'riot_id' => $riot_id,
                    'region' => $registrar->options['region']->value ?? 'lan',
                    'is_admin' => false, // or true, depending on the user
                ]
            );

            if (!$user->wasRecentlyCreated) {
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title('Cuenta de Riot ya registrada')
                      ->content("Parece que ya has registrado tu cuetna de lol, si deseas cambiarla, contacta a un administrador.")
                      ->build()
                );
            }
            $interaction->respondWithMessage(
                $this
                  ->message()
                  ->title('Registro de Riot account')
                  ->content("Tu cuenta {$user->riot_id} ha sido registrada con éxito.")
                  ->build()
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
                      ->content("https://www.leagueofgraphs.com/es/summoner/{$user->region}/{$parts[0]}-{$parts[1]}")
                      ->build()
                );
            }
            else
            {
                $interaction->respondWithMessage(
                    $this
                      ->message()
                      ->title("Cuenta de {$user->username}")
                      ->content("{$user->username} Aún no ha registrado su cuenta de League of Legends.\n\nPara registrarla, usa el comando /riot-account registrar")
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
                        'required' => false,
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
