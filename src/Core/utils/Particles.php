<?php
/**
 * Created by PhpStorm.
 * User: 20deavaults
 * Date: 10/9/18
 * Time: 8:33 AM
 */

namespace Core\utils;

use Core\Main;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\level\particle\FlameParticle;
use Core\player\PlayerClass;

class Particles{


    private $lines = null;

    public static function sendWings(PlayerClass $player){
        $map = [];
        $handle = Main::getInstance()->getResource("wings.map");
        $lines = explode("\n", rtrim(stream_get_contents($handle)));
        fclose($handle);

        $height = count($lines);
        foreach($lines as $lineNumber => $line){
            $len = strlen($line);
            for($i = 0; $i < $len; ++$i){
                if($line{$i} === "X"){
                    $map[] = new Vector2($i, $height - $lineNumber - 1);
                }
            }
        }

        $scale = 0.2;
        $particle = new FlameParticle(new Vector3);
        $yaw = $player->yaw / 180 * M_PI;
        $xFactor = -sin($yaw) * $scale;
        $zFactor = cos($yaw) * $scale;
        foreach($map as $vector){
            $particle->y = $vector->y;
            $particle->x = $xFactor * $vector->x;
            $particle->z = $zFactor * $vector->x;
            $player->getLevel()->addParticle($particle);
        }
    }
}