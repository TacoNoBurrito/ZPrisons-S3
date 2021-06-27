<?php namespace Taco\ZP\utils;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class DrugItems {

    public function isValidDrug(Item $item) : bool {
        return $item->getNamedTag()->hasTag("isDrug");
    }

    public function giveCrack(Player $player, int $amount) : void {
        $item = Item::get(ItemIds::SUGAR, 0, $amount);
        $item->clearCustomBlockData();
        $item->clearNamedTag();
        $item->setCustomName(TF::RESET.TF::BOLD.TF::WHITE."Crack");
        $item->setLore([TF::RESET.TF::GREEN."\nTap me on the ground whilst\ncrouching to consume me!\n\n".TF::GOLD."Effects: \n * Speed 3\n   * 15s"]);
        $item->getNamedTag()->setInt("isDrug", 1, true);
        $item->setNamedTagEntry(new ListTag("ench"));
        $player->getInventory()->addItem($item);
    }

    public function giveHeroin(Player $player, int $amount) : void {
        $item = Item::get(ItemIds::BLAZE_POWDER, 0, $amount);
        $item->clearCustomBlockData();
        $item->clearNamedTag();
        $item->setCustomName(TF::RESET.TF::BOLD.TF::WHITE."Smack");
        $item->setLore([TF::RESET.TF::GREEN."\nTap me on the ground whilst\ncrouching to consume me!\n\n".TF::GOLD."Effects: \n * Regeneration 5\n   * 10s\n * Slowness 1\n   * 3s"]);
        $item->getNamedTag()->setInt("isDrug", 1, true);
        $item->setNamedTagEntry(new ListTag("ench"));
        $player->getInventory()->addItem($item);
    }

    public function giveMolly(Player $player, int $amount) : void {
        $item = Item::get(ItemIds::GHAST_TEAR, 0, $amount);
        $item->clearCustomBlockData();
        $item->clearNamedTag();
        $item->setCustomName(TF::RESET.TF::BOLD.TF::WHITE."Molly");
        $item->setLore([TF::RESET.TF::GREEN."\nTap me on the ground whilst\ncrouching to consume me!\n\n".TF::GOLD."Effects: \n * Jump Boost 3\n   * 3s\n * Mining Fatigue 2\n   * 10s\n * Speed 2\n   * 15s"]);
        $item->getNamedTag()->setInt("isDrug", 1, true);
        $item->setNamedTagEntry(new ListTag("ench"));
        $player->getInventory()->addItem($item);
    }

    public function giveEffects(Player $player, Item $drug) : void {
        $name = $drug->getCustomName();
        $player->sendMessage(TF::GOLD." * DRUGS: Successfully Consumed Drug.");
        switch($name) {
            case TF::RESET.TF::BOLD.TF::WHITE."Molly":
                $effect = new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 20 * 3, 2);
                $player->addEffect($effect);
                $effect = new EffectInstance(Effect::getEffect(Effect::MINING_FATIGUE), 20 * 10, 1);
                $player->addEffect($effect);
                $effect = new EffectInstance(Effect::getEffect(Effect::SPEED), 20 * 15, 1);
                $player->addEffect($effect);
                break;
            case TF::RESET.TF::BOLD.TF::WHITE."Smack":
                $effect = new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 10, 4);
                $player->addEffect($effect);
                $effect = new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 20 * 3, 0);
                $player->addEffect($effect);
                break;
            case TF::RESET.TF::BOLD.TF::WHITE."Crack":
                $effect = new EffectInstance(Effect::getEffect(Effect::SPEED), 20 * 15, 2);
                $player->addEffect($effect);
                break;
        }
    }

}