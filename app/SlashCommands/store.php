<?php

namespace App\SlashCommands;

use Illuminate\Support\Facades\DB;
use Laracord\Commands\SlashCommand;
use Discord\Parts\Interactions\Command\Option;
use Discord\Parts\Interactions\Command\Choice;
use App\Models\Sugerencia;
use Carbon\Carbon;

include 'funciones.php';

class store extends SlashCommand
{
    /**
     * The slash command name.
     *
     * @var string
     */
    protected $name = 'store';

    /**
     * The slash command description.
     *
     * @var string
     */
    protected $description = 'Gasta tus capicoins en distintos articulos.';

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
        $listado = $interaction->data->options['listado'];
        $comprar = $interaction->data->options['comprar'];
        $productos = DB::table('productos')->get();

        if ($listado) {
            $nombre = "";
            $descripcion = "";
            $precio = "";
    
            foreach ($productos as $producto) {
                $nombre .= "{$producto->id} - {$producto->nombre} \n";
                $descripcion .= "{$producto->descripcion}\n";
                $precio .= "\${$producto->precio}\n";
            }
    
            $interaction->respondWithMessage(
                $this->message('Tienda')
                ->field('ID - Producto', $nombre)
                ->field('Descripción', $descripcion)
                ->field('Precio', $precio)
                ->build(),
            );
        } 

        if ($comprar) {
            $producto = $comprar->data->options['producto']->value;
            switch ($producto) {
                case 1:
                    $interaction->respondWithMessage(
                        $this->message()
                        ->title('Democracia')
                        ->content("Compra un voto extra para un campeón sugerido")
                        ->build(),
                    );
                    break;
                case 2:
                    $interaction->respondWithMessage(
                        $this->message()
                        ->title('Anarquía')
                        ->content('Quitale un voto a un campeón sugerido')
                        ->build(),
                    );
                    break;
                case 3:
                    $interaction->respondWithMessage(
                        $this->message()
                        ->title('Cállese alv')
                        ->content('Silencia un miembro (incluso al capibe) durante una hora')
                        ->build(),
                    );
                    break;
            }
        }

    }

    /**
     * Handle the slash command options.
     *
     * @param  \Discord\Parts\Interactions\Interaction  $interaction
     * @return void
     */
    public function options()
    {
        return [
            new Option($this->discord(), [
                'name' => 'listado',
                'description' => 'Ver los articulos disponibles en la tienda',
                'type' => Option::SUB_COMMAND,
            ]),
        ];
    }
}
