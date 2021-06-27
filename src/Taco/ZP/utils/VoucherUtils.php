<?php namespace Taco\ZP\utils;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class VoucherUtils {

    public function isValidMoneyVoucher(Item $item) : bool {
        return $item->getNamedTag()->hasTag("voucher");
    }

    public function getAmountOnMoneyVoucher(Item $item) : int {
        if (!$this->isValidMoneyVoucher($item)) return 0;
        return $item->getNamedTag()->getInt("amount");
    }

    public function giveMoneyVoucher(Player $player, int $amount) : void {
        $item = Item::get(ItemIds::PAPER);
        $item->setCustomName(TF::RESET.TF::GREEN."Money Voucher");
        $item->setLore([TF::RESET.TF::GREEN."Tap me on the ground whilst crouching to\nclaim this voucher worth: $".$amount]);
        $item->getNamedTag()->setInt("voucher", 1, true);
        $item->setNamedTagEntry(new ListTag("ench"));
        $item->getNamedTag()->setInt("amount", $amount);
        $player->getInventory()->addItem($item);
    }

}