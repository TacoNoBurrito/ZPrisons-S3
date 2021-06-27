<?php namespace Taco\ZP\ce\enchants;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Item;
use pocketmine\Player;
use Taco\ZP\ce\types\ArmorToggleEC;

class GearsEnchant extends ArmorToggleEC {

    public function onEquip(Player $player, Item $item) : void {
        $ins = new EffectInstance(Effect::getEffect(Effect::SPEED), INT32_MAX, $item->getEnchantmentLevel(70)-1);
        $player->addEffect($ins);
    }

    public function onDequip(Player $player, Item $item) : void {
        if ($player->hasEffect(Effect::NIGHT_VISION)) $player->removeEffect(Effect::NIGHT_VISION);
    }

}