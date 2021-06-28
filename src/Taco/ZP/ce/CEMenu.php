<?php namespace Taco\ZP\ce;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class CEMenu {

    public function openPickaxeMenu(Player $player) : void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $inv = $menu->getInventory();
        $pickaxe = $player->getInventory()->getItemInHand();
        $menu->setName("Upgrade Pickaxe CE'S");
        $inv->setItem(13, $pickaxe);
        $items = [
            0 => [
                "name" => "Speed",
                "id" => ItemIds::SUGAR,
                "price" => 10000,
                "maxLevel" => 2,
                "enchantID" => 70,
                "usage" => "Give yourself speed whilst holding\nthe item!"
            ],
            9 => [
                "name" => "Fortune",
                "id" => ItemIds::DIAMOND,
                "price" => 5000,
                "maxLevel" => 10,
                "enchantID" => 71,
                "usage" => "Get more items when you mine!"
            ],
            18 => [
                "name" => "Haste",
                "id" => ItemIds::DIAMOND_PICKAXE,
                "price" => 2500,
                "maxLevel" => 20,
                "enchantID" => 72,
                "usage" => "Mine even faster!"
            ],
            8 => [
                "name" => "Crates+",
                "id" => ItemIds::CHEST,
                "price" => 2500,
                "maxLevel" => 3,
                "enchantID" => 78,
                "usage" => "Get a higher chance to find crates!"
            ],
            17 => [
                "name" => "Squared",
                "id" => ItemIds::STONE,
                "price" => 35000,
                "maxLevel" => 10,
                "enchantID" => 79,
                "usage" => "Have a chance to create an explosion!"
            ]
        ];
        foreach ($items as $slot => $info) {
            $item = Item::get($info["id"]);
            $item->setCustomName(TF::RESET.$info["name"]);
            $item->setLore([
                TF::RESET.TF::GOLD."Price: $".$info["price"], TF::RESET.TF::GREEN."\n".$info["usage"]."\n\nMax Level: ".$info["maxLevel"]
            ]);
            $item->getNamedTag()->setInt("enchantID", $info["enchantID"]);
            $item->getNamedTag()->setInt("price", $info["price"]);
            $item->getNamedTag()->setInt("maxLevel", $info["maxLevel"]);
            $inv->setItem($slot, $item);
        }
        $menu->send($player);
        $menu->setListener(function(InvMenuTransaction $transaction) use($player, $pickaxe, $inv, $menu) : InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            $money = Loader::getInstance()->economyAPI->myMoney($player);
            if ($item->getNamedTag()->hasTag("price")) {
                $price = $item->getNamedTag()->getInt("price");
                if ($money >= $price) {
                    $id = $item->getNamedTag()->getInt("enchantID");
                    $newLevel = $pickaxe->hasEnchantment($id) ? $pickaxe->getEnchantmentLevel($id) + 1 : 1;
                    if ($newLevel > $item->getNamedTag()->getInt("maxLevel")) return $transaction->discard();
                    $string = "ce enchant {name} {id} {newLevel}";
                    Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace([
                        "{name}",
                        "{id}",
                        "{newLevel}"
                    ], [
                        "\"".$player->getName()."\"",
                        $id,
                        $newLevel
                    ], $string));
                    $pickaxe = $player->getInventory()->getItemInHand();
                    $inv->setItem(13, $pickaxe);
                    Loader::getInstance()->economyAPI->reduceMoney($player, $price);
                    $transaction->getPlayer()->removeWindow($transaction->getAction()->getInventory());
                    $this->openPickaxeMenu($player);
                }
            }
            return $transaction->discard();
        });
    }

    public function openArmorMenu(Player $player) : void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $inv = $menu->getInventory();
        $item = $player->getInventory()->getItemInHand();
        $menu->setName("Upgrade Armor CE'S");
        $inv->setItem(13, $item);
        $items = [
            0 => [
                "name" => "Glowing",
                "id" => ItemIds::ENDER_PEARL,
                "price" => 1000,
                "maxLevel" => 1,
                "enchantID" => 73,
                "usage" => "Give yourself Night Vision!",
                "type" => "helmet"
            ],
            9 => [
                "name" => "Gears",
                "id" => ItemIds::SUGAR,
                "price" => 2500,
                "maxLevel" => 2,
                "enchantID" => 74,
                "usage" => "Give yourself Speed!",
                "type" => "boots"
            ]
        ];
        foreach ($items as $slot => $info) {
            $itemm = Item::get($info["id"]);
            $itemm->setCustomName(TF::RESET.$info["name"]);
            $itemm->setLore([
                TF::RESET.TF::GOLD."Price: $".$info["price"], TF::RESET.TF::GREEN."\n".$info["usage"]."\n\nMax Level: ".$info["maxLevel"]."\n\nArmor Type: ".$info["type"]."\n\nNOTE: If you try to enchant a helmet\nwith the type chestplate, it will not work!"
            ]);
            $itemm->getNamedTag()->setInt("enchantID", $info["enchantID"]);
            $itemm->getNamedTag()->setInt("price", $info["price"]);
            $itemm->getNamedTag()->setInt("maxLevel", $info["maxLevel"]);
            $itemm->getNamedTag()->setString("type", $info["type"]);
            $inv->setItem($slot, $itemm);
        }
        $menu->send($player);
        $menu->setListener(function(InvMenuTransaction $transaction) use($player, $item, $inv, $menu) : InvMenuTransactionResult {
            $clicked = $transaction->getItemClicked();
            if (!$clicked->getNamedTag()->hasTag("type")) return $transaction->discard();
            $type = $clicked->getNamedTag()->getString("type");
            if ($type == "helmet" and !$this->isHelmet($item)) return $transaction->discard();
            if ($type == "chestplate" and !$this->isChestPlate($item)) return $transaction->discard();
            if ($type == "leggings" and !$this->isLeggings($item)) return $transaction->discard();
            if ($type == "boots" and !$this->isBoots($item)) return $transaction->discard();
            $money = Loader::getInstance()->economyAPI->myMoney($player);
            if ($clicked->getNamedTag()->hasTag("price")) {
                $price = $clicked->getNamedTag()->getInt("price");
                if ($money >= $price) {
                    $id = $clicked->getNamedTag()->getInt("enchantID");
                    $newLevel = $item->hasEnchantment($id) ? $item->getEnchantmentLevel($id) + 1 : 1;
                    if ($newLevel > $clicked->getNamedTag()->getInt("maxLevel")) return $transaction->discard();
                    $string = "ce enchant {name} {id} {newLevel}";
                    Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace([
                        "{name}",
                        "{id}",
                        "{newLevel}"
                    ], [
                        "\"".$player->getName()."\"",
                        $id,
                        $newLevel
                    ], $string));
                    Loader::getInstance()->economyAPI->reduceMoney($player, $price);
                    $transaction->getPlayer()->removeWindow($transaction->getAction()->getInventory());
                    $this->openArmorMenu($player);
                }
            }
            return $transaction->discard();
        });
    }

    public function isHelmet(Item $item) : bool {
        $helmetIds = [298, 302, 306, 310, 314];
        return in_array($item->getId(), $helmetIds);
    }

    public function isChestPlate(Item $item) : bool {
        $chestplateIds = [299, 303, 307, 311, 315];
        return in_array($item->getId(), $chestplateIds);
    }

    public function isLeggings(Item $item) : bool {
        $leggingsIds = [300, 304, 308, 312, 316];
        return in_array($item->getId(), $leggingsIds);
    }

    public function isBoots(Item $item) : bool {
        $bootsIds = [301, 305, 309, 313, 317];
        return in_array($item->getId(), $bootsIds);
    }

    public function openSwordMenu(Player $player) : void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $inv = $menu->getInventory();
        $pickaxe = $player->getInventory()->getItemInHand();
        $menu->setName("Upgrade Sword CE'S");
        $inv->setItem(13, $pickaxe);
        $items = [
            0 => [
                "name" => "Fire",
                "id" => ItemIds::FIRE,
                "price" => 15000,
                "maxLevel" => 2,
                "enchantID" => 77,
                "usage" => "Set the player on fire\nand immobilize them!"
            ],
            9 => [
                "name" => "Wither",
                "id" => 397,
                "price" => 5000,
                "maxLevel" => 5,
                "enchantID" => 75,
                "usage" => "Show the player the power of a\nWither!"
            ],
            18 => [
                "name" => "Fling",
                "id" => ItemIds::ARROW,
                "price" => 7500,
                "maxLevel" => 3,
                "enchantID" => 76,
                "usage" => "Make the player believe they can fly!"
            ]
        ];
        foreach ($items as $slot => $info) {
            $item = Item::get($info["id"]);
            $item->setCustomName(TF::RESET.$info["name"]);
            $item->setLore([
                TF::RESET.TF::GOLD."Price: $".$info["price"], TF::RESET.TF::GREEN."\n".$info["usage"]."\n\nMax Level: ".$info["maxLevel"]
            ]);
            $item->getNamedTag()->setInt("enchantID", $info["enchantID"]);
            $item->getNamedTag()->setInt("price", $info["price"]);
            $item->getNamedTag()->setInt("maxLevel", $info["maxLevel"]);
            $inv->setItem($slot, $item);
        }
        $menu->send($player);
        $menu->setListener(function(InvMenuTransaction $transaction) use($player, $pickaxe, $inv, $menu) : InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            $money = Loader::getInstance()->economyAPI->myMoney($player);
            if ($item->getNamedTag()->hasTag("price")) {
                $price = $item->getNamedTag()->getInt("price");
                if ($money >= $price) {
                    $id = $item->getNamedTag()->getInt("enchantID");
                    $newLevel = $pickaxe->hasEnchantment($id) ? $pickaxe->getEnchantmentLevel($id) + 1 : 1;
                    if ($newLevel > $item->getNamedTag()->getInt("maxLevel")) return $transaction->discard();
                    $string = "ce enchant {name} {id} {newLevel}";
                    Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace([
                        "{name}",
                        "{id}",
                        "{newLevel}"
                    ], [
                        "\"".$player->getName()."\"",
                        $id,
                        $newLevel
                    ], $string));
                    $pickaxe = $player->getInventory()->getItemInHand();
                    $inv->setItem(13, $pickaxe);
                    Loader::getInstance()->economyAPI->reduceMoney($player, $price);
                    $transaction->getPlayer()->removeWindow($transaction->getAction()->getInventory());
                    $this->openSwordMenu($player);
                }
            }
            return $transaction->discard();
        });
    }

}