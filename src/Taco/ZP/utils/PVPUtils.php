<?php namespace Taco\ZP\utils;

use pocketmine\entity\Entity;
use pocketmine\Player;
use Taco\ZP\Loader;
use Taco\ZP\randomEntities\DamageEntity;

class PVPUtils {

    public array $combatTag = [];

    public array $combo = [];

    public function combo(Player $player) : void {
        $this->combo[$player->getName()]++;
    }

    public function resetCombo(Player $player) : void {
        $this->combo[$player->getName()] = 0;
    }

    public function getCombo(Player $player) : int {
        return $this->combo[$player->getName()];
    }

    public function setInCombatTag(Player $player) : void {
        $this->combatTag[$player->getName()] = time();
    }

    public function isInCombatTag(Player $player) : bool {
        return (time() - $this->combatTag[$player->getName()] < 12);
    }

    public function getCombatTime(Player $player) : int {
        $t = time() - $this->combatTag[$player->getName()];
        return 12 - $t;
    }

    public function spawnDamageEntity(Player $player, Player $damaged, string $damage) : void {
        $pos = $player;
        Loader::getInstance()->getServer()->getDefaultLevel()->loadChunk($pos->getX() >> 16, $pos->getZ() >> 16);
        $frontBlock = $this->getFrontBlock($damaged);
        $nbt = Entity::createBaseNBT($frontBlock, null, 0, 0);
        $entity = new DamageEntity(Loader::getInstance()->getServer()->getDefaultLevel(), $nbt, $damaged, (string)$damage);
        $entity->spawnToAll();
    }

    //NOTE: doesn't actually get the front block. lol
    public function getFrontBlock(Player $player, $y = 0){
        $dv =$player->getDirectionVector();
        $pom = mt_rand(1,2);
        if ($pom == 1) $pom = -1+rand(0.5,1);
        if ($pom == 2) $pom = 1+rand(0.5,1);
        $pom1 = mt_rand(1,2);
        if ($pom1 == 1) $pom1 = -0.4+rand(0.5,0.4);
        if ($pom1 == 2) $pom1 = -0.6+rand(0.2,0.3);
        $pos = $player->asVector3()->add($dv->x - $pom, $y + 1, $dv->z+$pom1);
        return $player->getLevel()->getBlock($pos);
    }

}