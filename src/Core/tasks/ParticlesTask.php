<?php
/**
 * Created by PhpStorm.
 * User: Savion
 * Date: 5/23/2017
 * Time: 5:49 PM
 */

namespace Core\tasks;


use Core\Main;
use Core\player\PlayerClass;
use Core\utils\Particles;
use Core\utils\TextToHead;
use pocketmine\math\Vector3;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;

class ParticlesTask extends Task {

    private $ft, $vec3, $plugin;

    public function __construct(Main $plugin, Vector3 $vec3){
        $this->plugin = $plugin;
        $this->vec3 = $vec3;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun(): void{
        if($this->ft instanceof FloatingTextParticle and $this->ft !== null){
            foreach($this->getPlugin()->getServer()->getOnlinePlayers() as $player){
                if($player instanceof PlayerClass){
                    $this->ft->setInvisible(true);
                    $this->getPlugin()->getServer()->getWorldManager()->getDefaultWorld()->addParticle($this->vec3, $this->ft, [$player]);
                    $this->ft->setInvisible(false);
                    $text = TextToHead::sendFloatingText($player);
                    $string = $text[0]."\n".$text[1]."\n".$text[2]."\n".$text[3]."\n".$text[4]."\n".$text[5]."\n".$text[6]."\n".$text[7]."\n";
                    $this->ft->setText($string.TF::AQUA."Welcome ".$player->getRealName()."!");
                    $this->getPlugin()->getServer()->getWorldManager()->getDefaultWorld()->addParticle($this->vec3, $this->ft, [$player]);
                }
            }
        }else{
            foreach($this->getPlugin()->getServer()->getOnlinePlayers() as $player){
                if($player instanceof PlayerClass){
                    $ft = new FloatingTextParticle($this->vec3, "");
                    $this->getPlugin()->getServer()->getWorldManager()->getDefaultWorld()->addParticle($this->vec3, $ft, [$player]);
                    $text = TextToHead::sendFloatingText($player);
                    $string = $text[0]."\n".$text[1]."\n".$text[2]."\n".$text[3]."\n".$text[4]."\n".$text[5]."\n".$text[6]."\n".$text[7]."\n";
                    $ft->setText($string.TF::AQUA."Welcome ".$player->getRealName()."!");
                    $this->getPlugin()->getServer()->getWorldManager()->getDefaultWorld()->addParticle($this->vec3, $ft, [$player]);
                    $this->ft = $ft;
                }
            }
        }
    }
}