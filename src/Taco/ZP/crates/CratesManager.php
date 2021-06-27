<?php namespace Taco\ZP\crates;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\Position;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use Taco\ZP\Loader;

class CratesManager {

    const TYPE_COMMON = 0;
    const TYPE_UNCOMMON = 1;
    const TYPE_RARE = 2;
    const TYPE_LEGENDARY = 3;

    const TYPE_TO_TEXT = [
        self::TYPE_COMMON => "§r§aCommon",
        self::TYPE_UNCOMMON => "§r§3Un-Common",
        self::TYPE_RARE => "§9Rare",
        self::TYPE_LEGENDARY => "§r§l§gLEGENDARY"
    ];

    public function giveCrate(Player $player, int $type) : void {
        $item = Item::get(ItemIds::CHEST);
        $item->setCustomName(self::TYPE_TO_TEXT[$type]."§r§7 Crate");
        $item->setLore(["§r§aTap this on the ground\nwhilst crouching to open\nthe crate!"]);
        $item->getNamedTag()->setInt("type", $type);
        $item->getNamedTag()->setInt("validCrate", 1);
        $item->setNamedTagEntry(new ListTag("ench"));
        $player->getInventory()->addItem($item);
    }

    public function isValidCrate(Item $item) : bool {
        return $item->getNamedTag()->hasTag("validCrate");
    }

    public function giveRewardsOfCrate(Player $player, int $type, Position $cratePos) : void {

            $pos = $cratePos;
            Loader::getInstance()->getServer()->getDefaultLevel()->loadChunk($pos->getX() >> 16, $pos->getZ() >> 16);
            $nbt = Entity::createBaseNBT($pos, null, 0, 0);
            $entity = new CratesFireworkEntity($player->getLevel(), $nbt);
            $entity->spawnToAll();

        $random = mt_rand(1, 4);
        switch($type) {
            case self::TYPE_COMMON:
                $item1 = Item::get(ItemIds::IRON_PICKAXE);
                $item1->setCustomName("§l§aCommon Pickaxe");
                $enchant = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 1);
                $item1->addEnchantment($enchant);
                $item2 = Item::get(ItemIds::DIAMOND_SWORD);
                $item2->setCustomName("§l§aCommon Sword");
                $enchant = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 1);
                $item2->addEnchantment($enchant);
                if ($random == 1) {
                    $player->getInventory()->addItem($item1);
                    $player->sendMessage("§aYou won a Common Pickaxe!");
                } else if ($random == 2) {
                    Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "givemoney \"".$player->getName()."\" 2500");
                    $player->sendMessage("§aYou won $2500!");
                } else if ($random == 3) {
                    $player->getInventory()->addItem($item2);
                    $player->sendMessage("§aYou won a Common Sword!");
                } else if ($random == 4) {
                    $this->giveCrate($player, self::TYPE_UNCOMMON);
                    $player->sendMessage("§aYou won a Uncommon Crate!");
                }
                break;
            case self::TYPE_UNCOMMON:
                $item1 = Item::get(ItemIds::DIAMOND_PICKAXE);
                $item1->setCustomName("§l§aUn-Common Pickaxe");
                $enchant = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 2);
                $item1->addEnchantment($enchant);
                $item2 = Item::get(ItemIds::DIAMOND_SWORD);
                $item2->setCustomName("§l§aUn-Common Sword");
                $enchant = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 3);
                $item2->addEnchantment($enchant);
                if ($random == 1) {
                    $player->getInventory()->addItem($item1);
                    $player->sendMessage("§aYou won a Un-Common Pickaxe!");
                } else if ($random == 2) {
                    Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "givemoney \"".$player->getName()."\" 10000");
                    $player->sendMessage("§aYou won $10000!");
                } else if ($random == 3) {
                    $player->getInventory()->addItem($item2);
                    $player->sendMessage("§aYou won a Un-Common Sword!");
                } else if ($random == 4) {
                    $this->giveCrate($player, self::TYPE_RARE);
                    $player->sendMessage("§aYou won a Rate Crate!");
                }
                break;
            case self::TYPE_RARE:
                $item1 = Item::get(ItemIds::DIAMOND_PICKAXE);
                $item1->setCustomName("§l§aRare Pickaxe");
                $enchant = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 3);
                $item1->addEnchantment($enchant);
                $item2 = Item::get(ItemIds::DIAMOND_SWORD);
                $item2->setCustomName("§l§aRare Sword");
                $enchant = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 4);
                $item2->addEnchantment($enchant);
                if ($random == 1) {
                    $player->getInventory()->addItem($item1);
                    $player->sendMessage("§aYou won a Rare Pickaxe!");
                } else if ($random == 2) {
                    Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "givemoney \"".$player->getName()."\" 25000");
                    $player->sendMessage("§aYou won $25000!");
                } else if ($random == 3) {
                    $player->getInventory()->addItem($item2);
                    $player->sendMessage("§aYou won a Rare Sword!");
                } else if ($random == 4) {
                    $this->giveCrate($player, self::TYPE_LEGENDARY);
                    $player->sendMessage("§aYou won a Legendary Crate!");
                }
                break;
            case self::TYPE_LEGENDARY:
                $prot = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 4);
                $unb = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 4);
                $sword = Item::get(ItemIds::DIAMOND_SWORD);
                $sword->setCustomName("§l§6LEGENDARY Sword");
                $enchant = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 5);
                $sword->addEnchantment($enchant);
                $pickaxe = Item::get(ItemIds::DIAMOND_PICKAXE);
                $pickaxe->setCustomName("§l§6LEGENDARY Pickaxe");
                $enchant = new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 5);
                $pickaxe->addEnchantment($enchant);
                $helmet = Item::get(ItemIds::DIAMOND_HELMET);
                $helmet->setCustomName("§l§6LEGENDARY Helmet");
                $helmet->addEnchantment($prot);
                $helmet->addEnchantment($unb);
                $chestplate = Item::get(ItemIds::DIAMOND_CHESTPLATE);
                $chestplate->setCustomName("§l§6LEGENDARY Chestplate");
                $chestplate->addEnchantment($prot);
                $chestplate->addEnchantment($unb);
                $leggings = Item::get(ItemIds::DIAMOND_LEGGINGS);
                $leggings->setCustomName("§l§6LEGENDARY Leggings");
                $leggings->addEnchantment($prot);
                $leggings->addEnchantment($unb);
                $boots = Item::get(ItemIds::DIAMOND_BOOTS);
                $boots->setCustomName("§l§6LEGENDARY Boots");
                $boots->addEnchantment($prot);
                $boots->addEnchantment($unb);
                $random = mt_rand(1, 5);
                switch($random) {
                    case 1:
                        $player->sendMessage("§aYou have gotten the §l§6LEGENDARY Sword!");
                        Loader::getInstance()->getServer()->broadcastMessage("§a".$player->getName()." has gotten a §l§6LEGENDARY Sword §r§afrom a §l§6LEGENDARY §r§aCrate!");
                        $player->getInventory()->addItem($sword);
                        break;
                    case 2:
                        $player->sendMessage("§aYou have gotten the §l§6LEGENDARY Helmet!");
                        Loader::getInstance()->getServer()->broadcastMessage("§a".$player->getName()." has gotten a §l§6LEGENDARY Helmet §r§afrom a §l§6LEGENDARY §r§aCrate!");
                        $player->getInventory()->addItem($helmet);
                        break;
                    case 3:
                        $player->sendMessage("§aYou have gotten the §l§6LEGENDARY Chestplate!");
                        Loader::getInstance()->getServer()->broadcastMessage("§a".$player->getName()." has gotten a §l§6LEGENDARY Chestplate §r§afrom a §l§6LEGENDARY §r§aCrate!");
                        $player->getInventory()->addItem($chestplate);
                        break;
                    case 4:
                        $player->sendMessage("§aYou have gotten the §l§6LEGENDARY Leggings!");
                        Loader::getInstance()->getServer()->broadcastMessage("§a".$player->getName()." has gotten a §l§6LEGENDARY Leggings §r§afrom a §l§6LEGENDARY §r§aCrate!");
                        $player->getInventory()->addItem($leggings);
                        break;
                    case 5:
                        $player->sendMessage("§aYou have gotten the §l§6LEGENDARY Boots!");
                        Loader::getInstance()->getServer()->broadcastMessage("§a".$player->getName()." has gotten a §l§6LEGENDARY Boots §r§afrom a §l§6LEGENDARY §r§aCrate!");
                        $player->getInventory()->addItem($boots);
                        break;
                }
                break;
        }
    }

}