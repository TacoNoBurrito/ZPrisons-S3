<?php namespace Taco\ZP\ce\enchants;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Taco\ZP\Loader;

class FlingEnchant extends MeleeWeaponEnchantment {

    public function isApplicableTo(Entity $victim) : bool{
        return $victim instanceof Living;
    }

    public function getDamageBonus(int $enchantmentLevel) : float{
        return 0;
    }

    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel) : void{
        if ($victim instanceof Player && $attacker instanceof Player) {
            if (mt_rand(1, 75) <= $enchantmentLevel) {
                $victim->sendPopup("§l§6YOU HAVE BEEN LAUNCHED");
                $eff = new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20 * 2, 0);
                $victim->addEffect($eff);
                $victim->setMotion(new Vector3($victim->getX(), 1.5, $victim->getZ()));
                Loader::getUtils()->spawnLightning($victim);
                $particle = new HugeExplodeParticle($victim);
                $victim->getLevel()->addParticle($particle);
            }
        }
    }
}