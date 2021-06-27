<?php namespace Taco\ZP\ce\enchants;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use pocketmine\Player;

class WitherEnchant extends MeleeWeaponEnchantment {

    public function isApplicableTo(Entity $victim) : bool{
        return $victim instanceof Living;
    }

    public function getDamageBonus(int $enchantmentLevel) : float{
        return 0;
    }

    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel) : void{
        if ($victim instanceof Player && $attacker instanceof Player) {
            if (mt_rand(1, 75) <= $enchantmentLevel) {
                $victim->sendPopup("§l§7THE WITHER IS HERE");
                $eff = new EffectInstance(Effect::getEffect(Effect::WITHER), 20 * 3, 1);
                $victim->addEffect($eff);
            }
        }
    }
}