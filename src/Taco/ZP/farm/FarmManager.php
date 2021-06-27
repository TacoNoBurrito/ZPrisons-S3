<?php namespace Taco\ZP\farm;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use Taco\ZP\farm\entities\CowEntity;
use Taco\ZP\farm\entities\PigEntity;
use Taco\ZP\Loader;

class FarmManager {

    public array $animalSpawnPositions = [
        "233:65:282",
        "231:65:269",
        "236:65:261",
        "246:65:265"
    ];

    public function init() : void {
        $pos = $this->generateRandomSpawnPoint();
        Loader::getInstance()->getServer()->getLevelByName("farm")->loadChunk($pos->getX() >> 16, $pos->getZ() >> 16);
        $nbt = Entity::createBaseNBT($pos, null, 0, 0);
        $entity = new CowEntity(Loader::getInstance()->getServer()->getLevelByName("farm"), $nbt);
        $entity->spawnToAll();
        $pos = $this->generateRandomSpawnPoint();
        Loader::getInstance()->getServer()->getLevelByName("farm")->loadChunk($pos->getX() >> 16, $pos->getZ() >> 16);
        $nbt = Entity::createBaseNBT($pos, null, 0, 0);
        $entity = new PigEntity(Loader::getInstance()->getServer()->getLevelByName("farm"), $nbt);
        $entity->spawnToAll();
    }

    public function generateRandomSpawnPoint() : Vector3 {
        $AMOGUS = explode(":", $this->animalSpawnPositions[array_rand($this->animalSpawnPositions)]);
        return new Vector3((int)$AMOGUS[0], (int)$AMOGUS[1], (int)$AMOGUS[2]);
    }

}