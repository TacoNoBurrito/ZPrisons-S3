<?php namespace Taco\ZP\utils;

use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use Taco\ZP\Loader;

class AreaUtils {

    public function isInPVP(Position $pos) : bool {
        $true = false;
        foreach(Loader::getInstance()->areas as $name => $info) {
            if ($this->isWithinTwoFormattedPoints($pos, $info["pos1"], $info["pos2"])) {
                $true = true;
                break;
            }
        }
        return $true;
    }

    public function addPvPArena(string $name, string $pos1, string $pos2) : void {
        Loader::getInstance()->areaDB->set($name, [
            "pos1" => $pos1,
            "pos2" => $pos2
        ]);
        Loader::getInstance()->areaDB->save();
        Loader::getInstance()->areas[$name] = [
            "pos1" => $pos1,
            "pos2" => $pos2
        ];
    }

    public function formatPos(Position $pos) : string {
        return $pos->getFloorX().":".$pos->getFloorY().":".$pos->getFloorZ();
    }

    public function formattedToVec3(string $pos) : Vector3 {
        $p = explode(":", $pos);
        return new Vector3((int)$p[0], (int)$p[1], (int)$p[2]);
    }

    public function isWithinTwoFormattedPoints(Position $pos, string $pos1, string $pos2) : bool {
        $exp1 = explode(":", $pos1);
        $exp2 = explode(":", $pos2);
        $x1 = min((int)$exp1[0], (int)$exp2[0]);
        $y1 = min((int)$exp1[1], (int)$exp2[1]);
        $z1 = min((int)$exp1[2], (int)$exp2[2]);
        $x2 = max((int)$exp1[0], (int)$exp2[0]);
        $y2 = max((int)$exp1[1], (int)$exp2[1]);
        $z2 = max((int)$exp1[2], (int)$exp2[2]);
        $aabb = new AxisAlignedBB($x1, $y1, $z1, $x2, $y2, $z2);
        return $aabb->isVectorInside($pos);
    }

}