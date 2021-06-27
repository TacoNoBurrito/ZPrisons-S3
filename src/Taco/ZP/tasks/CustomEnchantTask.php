<?php namespace Taco\ZP\tasks;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Pickaxe;
use pocketmine\scheduler\Task;
use Taco\ZP\Loader;

class CustomEnchantTask extends Task {

    public function onRun(int $currentTick) : void {
        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $item = $player->getInventory()->getItemInHand();
            if ($item instanceof Pickaxe) {
                if ($item->hasEnchantment(70)) {
                    $level = $item->getEnchantmentLevel(70);
                    $eff = new EffectInstance(Effect::getEffect(Effect::SPEED), 30, $level - 1);
                    $player->addEffect($eff);
                }
                if ($item->hasEnchantment(72)) {
                    $level = $item->getEnchantmentLevel(72);
                    $eff = new EffectInstance(Effect::getEffect(Effect::HASTE), 30, $level - 1);
                    $player->addEffect($eff);
                }
            }
        }
    }

}