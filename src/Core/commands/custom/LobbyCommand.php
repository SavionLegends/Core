<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 9/11/18
 * Time: 10:20 AM
 */

namespace Core\commands\custom;

use Core\commands\CoreCommand;
use Core\Main;
use Core\player\PlayerClass;
use Core\utils\Permissions;
use Core\utils\Prefix;
use Core\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;

class LobbyCommand extends CoreCommand{

    private $plugin, $server;

    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::LOBBY_COMMAND);
    }

    public function getPlugin(): Main{
        return $this->getPlugin();
    }

    public function getServer(): \pocketmine\Server{
        return $this->server;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if($sender instanceof PlayerClass){
            Utils::sendLobbyItems($sender);
        }
        return true;
    }
}
