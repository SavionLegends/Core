<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 5/10/2017
 * Time: 6:51 PM
 */

namespace Core\commands\custom;

use Core\Main;
use Core\player\PlayerClass;
use Core\commands\CoreCommand;

use Core\utils\Permissions;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class LangCommand extends CoreCommand{

    private Main $plugin;
    private Server $server;

    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::LANG_COMMAND);
    }

    /**
     * @return Main
     */
    public function getPlugin() : Main{
        return $this->plugin;
    }

    /**
     * @return Server
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
        if(isset($args[0])){
            if(isset(Main::$langs[$args[0]])){
                if(is_string($args[0])){
                    if($sender instanceof PlayerClass){
                        $sender->setLang($args[0]);
                        $sender->sendMessage("Set language preference to ".Main::$langs[$args[0]]);
                        //TODO: implement language preference formatting
                    }
                }
            }
        }
        return true;
    }
}