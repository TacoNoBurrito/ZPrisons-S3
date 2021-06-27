<?php namespace Taco\ZP\leaderboards;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use Taco\ZP\ft\FloatingTextEntity;
use Taco\ZP\Loader;

class LeaderboardUtils {

    private array $leaderboards = [
        "money" => [
            "x" => 270,
            "y" => 71,
            "z" => 248
        ],
        "gangs" => [
            "x" => 244,
            "y" => 71,
            "z" => 248
        ]
    ];

    public function registerLeaderboards() : void {
        foreach($this->leaderboards as $name => $array) {
            $pos = new Vector3($array["x"], $array["y"], $array["z"]);
            Loader::getInstance()->getServer()->getDefaultLevel()->loadChunk($pos->getX() >> 16, $pos->getZ() >> 16);
            $nbt = Entity::createBaseNBT($pos, null, 0, 0);
            $entity = new LeaderboardEntity(Loader::getInstance()->getServer()->getDefaultLevel(), $nbt, $name);
            $entity->spawnToAll();
        }
    }

}