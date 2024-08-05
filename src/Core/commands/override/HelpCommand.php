<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 5/12/2017
 * Time: 4:17 PM
 */

namespace Core\commands\override;


use Core\Main;
use Core\utils\Permissions;
use Core\utils\Prefix;
use pocketmine\command\CommandSender;
use Core\commands\CoreCommand;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;

class HelpCommand extends CoreCommand{

    private $plugin, $server;

    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::HELP_COMMAND);
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
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        $sender->sendMessage(Prefix::DEFAULT."No help pages currently!");
        return true;
    }
}