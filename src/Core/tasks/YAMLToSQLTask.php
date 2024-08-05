<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 10/15/18
 * Time: 9:48 AM
 */

namespace Core\tasks;

use Core\Main;
use Core\utils\YAMLToSQL;
use pocketmine\scheduler\Task;

class YAMLToSQLTask extends Task{

    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun(int $currentTick){
        $sql = new YAMLToSQL($this->getPlugin());
        $sql->process();
    }
}