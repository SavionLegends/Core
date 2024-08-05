<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 10/15/18
 * Time: 8:40 AM
 */

namespace Core\utils;



use Core\Main;
use pocketmine\utils\Config;

class YAMLToSQL{

    private $plugin, $db, $users, $result;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        $this->db = new \mysqli(!is_null($this->plugin->settings->get("server-name")) ? $this->plugin->settings->get("server-name") : "localhost", $this->plugin->settings->get("username"), $this->plugin->settings->get("password"), $this->plugin->settings->get("db_name"), !is_null($this->plugin->settings->get("port")) ? ((int)$this->plugin->settings->get("port")) : 3306);
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getServer(){
        return $this->plugin->getServer();
    }

    /**
     *
     */
    public function process(){
        $this->getPlugin()->database->reload();
        $database = $this->getPlugin()->database->getAll();
        $total = count($database);
        $keys = array_keys($database);
        $i = 0;
        while($i < $total){
            $name = $database[$keys[$i]]["name"];
            $password = $database[$keys[$i]]["password"];
            if($database[$keys[$i]]["uploaded_to_sql"] !== true){
                $this->result = $this->db->query("INSERT INTO sql9260847.registered_players(name, password)
										VALUES ('$name', '$password')"
                );
                if($this->result === false){
                    $this->getPlugin()->getLogger()->info(Prefix::DEFAULT_BAD."Files weren't uploaded to SQL server!");
                    break;
                }
                $database[$keys[$i]]["uploaded_to_sql"] = true;
                $this->plugin->database->setAll($database);
                $this->plugin->database->save();
                $this->users++;
            }else{
                $this->getPlugin()->getLogger()->info(Prefix::DEFAULT."Player ".$name." has already been uploaded to the SQL server");
            }
            $i++;
        }
        if($this->result){
            $this->getPlugin()->getLogger()->info(Prefix::DEFAULT.(string) $this->users . " user files uploaded to SQL database.");
        }
    }
}