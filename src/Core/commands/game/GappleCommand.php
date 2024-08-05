<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 10/16/18
 * Time: 8:45 AM
 */

namespace Core\commands\game;


use Core\commands\CoreCommand;
use Core\Main;
use Core\managers\GameManager;
use Core\player\PlayerClass;
use Core\utils\Permissions;
use Core\utils\Prefix;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;

class GappleCommand extends CoreCommand{

    private $plugin, $server;

   public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
       parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
       $this->plugin = $plugin;
       $this->server = $plugin->getServer();
       $this->setPermission(Permissions::ALL_PERMS);
   }


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
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        $sender->sendMessage(Prefix::DEFAULT."In progress...");
        return true;
    }
}