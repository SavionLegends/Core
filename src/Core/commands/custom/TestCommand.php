<?php

/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 10/3/18
 * Time: 9:11 AM
 */

namespace Core\commands\custom;

use Core\Main;
use Core\commands\CoreCommand;
use Core\utils\Permissions;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

class TestCommand extends CoreCommand{

    private $plugin, $server, $name;

    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($plugin, $name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
        $this->server = $plugin->getServer();
        $this->setPermission(Permissions::DEFAULT);
    }

    public function getPlugin() : Main{
        return $this->plugin;
    }

    public function getServer(){
        return $this->server;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        return true;
    }
}