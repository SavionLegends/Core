<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 4/29/2017
 * Time: 12:57 PM
 */

namespace Core\commands\override;

use Core\Main;

use Core\utils\Permissions;
use Core\utils\Prefix;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use Core\commands\CoreCommand;

class MeCommand extends CoreCommand{

    private $plugin;
    private $server;

    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::ME_COMMAND);
    }

    /**
     * @return Main
     */
    public function getPlugin() : Main{
        return $this->plugin;
    }

    /**
     * @return \pocketmine\Server
     */
    public function getServer(){
        return $this->server;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
       $sender->sendMessage(TextFormat::RED.TextFormat::BOLD."YOU CANT DO THAT COMMAND HAHA!");
       return true;
    }
}