<?php namespace Taco\ZP\useful;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class InvMenus {

    public function openBlackMarketMenu(Player $player) : void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $inv = $menu->getInventory();
        $menu->setName("The Blackmarket");
        $drugTypes = [
            "Molly" => [
                "price" => 25000,
                "slot" => 4,
                "id:meta" => ItemIds::GHAST_TEAR.":0",
                "lore" => [
                    TF::RESET.TF::GREEN."$25000\nTap me on the ground whilst\ncrouching to consume me!\n\n".TF::GOLD."Effects: \n * Speed 3\n   * 15s"
                ]
            ],
            "Smack" => [
                "price" => 15000,
                "slot" => 13,
                "id:meta" => ItemIds::BLAZE_POWDER.":0",
                "lore" => [
                    TF::RESET.TF::GREEN."$15000\nTap me on the ground whilst\ncrouching to consume me!\n\n".TF::GOLD."Effects: \n * Regeneration 5\n   * 10s\n * Slowness 1\n   * 3s"
                ]
            ],
            "Crack" => [
                "price" => 20000,
                "slot" => 22,
                "id:meta" => ItemIds::SUGAR.":0",
                "lore" => [
                    TF::RESET.TF::GREEN."$20000\nTap me on the ground whilst\ncrouching to consume me!\n\n".TF::GOLD."Effects: \n * Speed 3\n   * 15s"
                ]
            ]
        ];
        foreach($drugTypes as $name => $info) {
            $id = explode(":", $info["id:meta"]);
            $inv->setItem($info["slot"], Item::get((int)$id[0], (int)$id[1])->setCustomName(TF::RESET.TF::WHITE.$name)->setLore($info["lore"]));
        }
        $menu->send($player);
        $money = Loader::getInstance()->economyAPI->myMoney($player);
        $menu->setListener(function(InvMenuTransaction $transaction) use($player, $menu, $drugTypes, $money) : InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            $replaced = str_replace(TF::RESET.TF::WHITE, "", $item->getCustomName());
            if (isset($drugTypes[$replaced])) {
                $price = $drugTypes[$replaced]["price"];
                if ($money >= $price) {
                    Loader::getInstance()->economyAPI->reduceMoney($player, $price);
                    $player->sendMessage(TF::GREEN."Successfully purchased: ".$replaced."!");
                    switch($replaced) {
                        case "Molly":
                            Loader::getDrugItems()->giveMolly($player, 1);
                            break;
                        case "Smack":
                            Loader::getDrugItems()->giveHeroin($player, 1);
                            break;
                        case "Crack":
                            Loader::getDrugItems()->giveCrack($player, 1);
                            break;
                    }
                } else {
                    $player->sendMessage(TF::RED."You do not have enough money to make this purchase!");
                }
            }
            return $transaction->discard();
        });
    }

}