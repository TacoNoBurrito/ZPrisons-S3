<?php namespace Taco\ZP\tasks;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Pickaxe;
use pocketmine\scheduler\Task;
use Taco\ZP\ce\CEManager;
use Taco\ZP\Loader;

class CustomEnchantTask extends Task {

    public function onRun(int $currentTick) : void {
        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $item = $player->getInventory()->getItemInHand();
            if ($player->hasEffect(Effect::SPEED)) {
                if ($player->getEffect(Effect::SPEED)->getAmplifier() > 5) $player->removeEffect(Effect::SPEED);
            }
            if ($item instanceof Pickaxe) {
                if ($item->hasEnchantment(72)) {
                    $level = $item->getEnchantmentLevel(72);
                    $eff = new EffectInstance(Effect::getEffect(Effect::HASTE), 30, $level - 1);
                    $player->addEffect($eff);
                }
                if ($item->hasEnchantment(70)) {
                    if ($player->getArmorInventory()->getBoots()->hasEnchantment(CEManager::CONVERSIONS["Gears"])) continue;
                        $level = $item->getEnchantmentLevel(70);
                    $eff = new EffectInstance(Effect::getEffect(Effect::SPEED), 30, $level - 1);
                    $player->addEffect($eff);
                }
            }
        }
    }

}