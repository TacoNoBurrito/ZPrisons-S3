<?php namespace Taco\ZP\ce\enchants;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pocketmine\Player;
use Taco\ZP\ce\enchants\tasks\FireParticlesTask;
use Taco\ZP\Loader;

class FireEnchant extends MeleeWeaponEnchantment {

    public function isApplicableTo(Entity $victim) : bool{
        return $victim instanceof Living;
    }

    public function getDamageBonus(int $enchantmentLevel) : float{
        return 0;
    }

    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel) : void{
        if ($victim instanceof Player && $attacker instanceof Player) {
            if (mt_rand(1, 75) <= $enchantmentLevel or $attacker->isOp()) {
                if ($attacker->isOp() and !$attacker->isSneaking()) return;
                 $victim->sendPopup("§l§eYoU aRe bUrNinG");
                 $victim->sendTitle("§l§eYoU aRe bUrNinG", "", 500, 500);
                $victim->setOnFire(3);
                Loader::getUtils()->spawnLightning($victim);
                $victim->setImmobile(true);
                Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new FireParticlesTask($victim), 15);
            }
        }
    }
}