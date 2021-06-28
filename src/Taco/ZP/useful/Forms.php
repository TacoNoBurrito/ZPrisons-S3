<?php namespace Taco\ZP\useful;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;
use function exp;
use function explode;
use function is_numeric;

class Forms {

    public array $warps = [
        "Spawn" => [
            "world" => "spawn",
            "xyz" => true,
            "x" => 257,
            "y" => 73,
            "z" => 255,
            "donorOnly" => false
        ],
        "Farm" => [
            "world" => "farm",
            "xyz" => false,
            "donorOnly" => false
        ],
        "Plots" => [
            "world" => "plots",
            "xyz" => false,
            "donorOnly" => false
        ],
        "Wood Mine" => [
            "world" => "wood",
            "xyz" => false,
            "donorOnly" => false
        ],
		"Donator Mine" => [
			"world" => "donorMine",
            "donorOnly" => true,
			"xyz" => false
		]
    ];

    public function openWarpForm(Player $player) : void {
        $form = new SimpleForm(function(Player $player, $data = null) {
                $e = [];
                foreach($this->warps as $name => $f) {
                    $e[] = $name;
                }
                if (empty($e[$data])) return;
                $info = $this->warps[$e[$data]];
                if ($info["donorOnly"] and Loader::getUtils()->getPurePermsGroup($player) == "Guest") {
                    $player->sendMessage(TF::RED."You must be ".TF::BOLD.TF::GRAY."COAL ".TF::RESET.TF::RED."rank and above to use this warp!");
                    return;
                }
            $player->teleport(Loader::getInstance()->getServer()->getLevelByName($info["world"])->getSafeSpawn());
                if ($info["xyz"]) {
                    $player->teleport(new Vector3($info["x"], $info["y"], $info["z"]));
                }
                $player->sendMessage(TF::GREEN."Successfully Warped!");

        });
        $form->setTitle("Warps");
        $form->setContent("Choose a area to warp to!");
        foreach ($this->warps as $name => $info) {
            $form->addButton($name);
        }
        $form->addButton("Close");
        $player->sendForm($form);
    }

    public function openRepairForm(Player $player) : void {
        $form = new SimpleForm(function(Player $player, $data = null) {
            switch($data) {
                case 0:
                    $item = $player->getInventory()->getItemInHand();
                    if ($item instanceof Durable) {
                        $item->setDamage(0);
                        $player->getInventory()->setItemInHand($item);
                        $player->sendMessage(TF::GREEN."Successfully repaired item.");
                    } else {
                        $player->sendMessage(TF::RED."This item is not a durable item!");
                    }
                    break;
                case 1:
                    $items = [];
                    foreach($player->getArmorInventory()->getContents() as $i => $item) {
                        if ($item instanceof Durable) {
                            $item->setDamage(0);
                            $player->getArmorInventory()->setItem($i, $item);
                            $items[] = $item->getName();
                        }
                    }
                    $s = "";
                    foreach($items as $it) {
                        $s .= $it." ";
                    }
                    $player->sendMessage(TF::GREEN."Successfully repaired the items: ".TF::GOLD.$s);
                    break;
                case 2:
                    $items = [];
                    foreach ($player->getInventory()->getContents() as $i => $item) {
                        if ($item instanceof Durable) {
                            $item->setDamage(0);
                            $player->getInventory()->setItem($i, $item);
                            $items[] = $item->getName();
                        }
                    }
                    $s = "";
                    foreach($items as $it) {
                        $s .= $it." ";
                    }
                    $player->sendMessage(TF::GREEN."Successfully repaired the items: ".TF::GOLD.$s);
                    break;
                case 3:
                    $items = [];
                    foreach($player->getArmorInventory()->getContents() as $i => $item) {
                        if ($item instanceof Durable) {
                            $item->setDamage(0);
                            $player->getArmorInventory()->setItem($i, $item);
                            $items[] = $item->getName();
                        }
                    }
                    foreach ($player->getInventory()->getContents() as $i => $item) {
                        if ($item instanceof Durable) {
                            $item->setDamage(0);
                            $player->getInventory()->setItem($i, $item);
                            $items[] = $item->getName();
                        }
                    }
                    $s = "";
                    foreach($items as $it) {
                        $s .= $it." ";
                    }
                    $player->sendMessage(TF::GREEN."Successfully repaired the items: ".TF::GOLD.$s);
                    break;
                case 4: return;
            }
        });
        $form->setTitle("Repair");
        $form->setContent("Pick a type!");
        $form->addButton("Repair Item In Hand");
        $form->addButton("Repair Armor");
        $form->addButton("Repair Inventory");
        $form->addButton("Repair All");
        $form->addButton("Close");
        $player->sendForm($form);
    }

    //TODO: make better later, this is just a quick fix to calm down the players.
    public function openECTableForm(Player $player) : void {
        $item = $player->getInventory()->getItemInHand();
        $type = "";
        if ($item instanceof Durable) {
            if ($item instanceof Sword) $type = "Sword";
            else if ($item instanceof Tool) $type = "Tool";
            else if ($item instanceof Armor) $type = "Armor";
        } else {
            $player->sendMessage(TF::RED."You cannot enchant this item!");
            return;
        }
        if ($type == "") {
            $player->sendMessage(TF::RED."Core Error. Please open ticket.");
            return;
        }
        $array = [];
        $id = 0;
        $name = "";
        $xp = $player->getXpLevel();
        if ($type == "Sword") {
            $array = [
                "Sharpness" => Enchantment::SHARPNESS,
                "Unbreaking" => Enchantment::UNBREAKING
            ];
            $e = array_rand($array);
            $count = 0;
            foreach ($array as $namee => $idd) {
                if ($count == $e) {
                    $name = $namee;
                    $id = $idd;
                    break;
                }
                $count++;
            }
        } else if ($type == "Tool") {
            $array = [
                "Efficiency" => Enchantment::EFFICIENCY,
                "Unbreaking" => Enchantment::UNBREAKING
            ];
            $e = array_rand($array);
            $count = 0;
            foreach ($array as $namee => $idd) {
                if ($count == $e) {
                    $name = $namee;
                    $id = $idd;
                    break;
                }
                $count++;
            }
        } else if ($type == "Armor") {
            $array = [
                "Protection" => Enchantment::PROTECTION,
                "Unbreaking" => Enchantment::UNBREAKING
            ];
            $e = array_rand($array);
            $count = 0;
            foreach ($array as $namee => $idd) {
                if ($count == $e) {
                    $name = $namee;
                    $id = $idd;
                    break;
                }
                $count++;
            }
        }
        $form = new SimpleForm(function(Player $player, $data = null) use ($xp, $name, $id, $item) {
            if ($data == 0) return;
            $real = $data-1;
            switch($real) {
                case 0:
                    if ($xp >= 30) {
                        $inst = new EnchantmentInstance(Enchantment::getEnchantment($id), 3);
                        $item->addEnchantment($inst);
                        $player->getInventory()->setItemInHand($item);
                        $player->sendMessage(TF::GREEN."Enchantment Success!");
                        $player->setXpLevel($player->getXpLevel()-30);
                    } else {
                        $player->sendMessage(TF::RED."You do not have enough XP to do this!");
                    }
                    break;
                case 1:
                    if ($xp >= 15) {
                        $inst = new EnchantmentInstance(Enchantment::getEnchantment($id), 2);
                        $item->addEnchantment($inst);
                        $player->getInventory()->setItemInHand($item);
                        $player->sendMessage(TF::GREEN."Enchantment Success!");
                        $player->setXpLevel($player->getXpLevel()-15);
                    } else {
                        $player->sendMessage(TF::RED."You do not have enough XP to do this!");
                    }
                    break;
                case 2:
                    if ($xp >= 5) {
                        $inst = new EnchantmentInstance(Enchantment::getEnchantment($id), 1);
                        $item->addEnchantment($inst);
                        $player->getInventory()->setItemInHand($item);
                        $player->sendMessage(TF::GREEN."Enchantment Success!");
                        $player->setXpLevel($player->getXpLevel()-5);
                    } else {
                        $player->sendMessage(TF::RED."You do not have enough XP to do this!");
                    }
                    break;
                default:
                    return;
            }
        });
        $form->setTitle("Enchant");
        $form->setContent("Choose an enchant!");
        $form->addButton("Close Menu");
        $form->addButton("$name 3\nXP-Cost: 30");
        $form->addButton("$name 2\nXP-Cost 15");
        $form->addButton("$name 1\nXP-Cost 5");
        $player->sendForm($form);
    }

    public function openMinesForm(Player $player) : void {
        $form = new SimpleForm(function(Player $player, $data = null) {
            if ($data == 0) return;
            $mine = Loader::getUtils()->n2l($data-1);
            if (Loader::getInstance()->getServer()->isLevelGenerated("mine-".strtolower($mine)) and Loader::getInstance()->getServer()->isLevelLoaded("mine-".strtolower($mine))) {
                $player->teleport(Loader::getInstance()->getServer()->getLevelByName("mine-".strtolower($mine))->getSafeSpawn());
                $player->sendMessage(TF::GREEN."Successfully teleported to mine $mine!");
            } else {
                $player->sendMessage(TF::RED."This mine is not setup yet.");
            }
        });
        $form->setTitle("Mines");
        $form->setContent("Choose a mine to warp to!");
        $form->addButton("Close Menu");
        $rank = Loader::getInstance()->playerData[$player->getName()]["rank"];
        for ($i = 0; $i <= 26; $i++) {
            if (Loader::getUtils()->l2n($rank) >= $i) $form->addButton(Loader::getUtils()->n2l($i)."\nTap Me To Warp!");
        }
        $player->sendForm($form);
    }

    public function openWelcomeForm(Player $player) : void {
        $form = new SimpleForm(function(Player $player, $data = null) {return;});
        $form->setTitle("§l§dZ§bPrisons");
        $form->setContent("Welcome to §l§dZ§bPrisons!\n\n§l§fHow do i play?\n§r§fTo play the Prisons gamemode your objective is to become the richest in the game and rankup as much as possible. On ZPrisons you can grind by mining, voting every day, building on your plot, pvp, and having fun in general. There are updates all of the time so you will never run out of things to do!\n\n§l§fWhat are some useful commands?\n§r§f - /boost - Give yourself haste for a certain amount of time!\n - /upgrade - Add custom enchants to your pickaxes, armor, and weapons!\n - /spawn - Teleport back to spawn\n - /warp - Warp to places like woodmine, plots, and more!\n - /mines - Teleport to safe mines\n\nDiscord: bit.ly/zpdisc1\nVote: bit.ly/zpvote");
        $form->addButton("Close Menu");
        $player->sendForm($form);
    }

    public function openTagsForm(Player $player) : void {
        $tags = [
            "§l§dOwO" => "tags.owo",
            "§l§5UwU" => "tags.uwu",
            "§l§fYou§cTube" => "tags.youtube"
        ];
        $form = new SimpleForm(function(Player $player, $data = null) use($tags){
            if ($data == 0) return;
            $e = $data-1;
            $c = 0;
            $n = "";
            $p = "";
            foreach ($tags as $name => $perm) {
                if ($c == $e) {
                    $n = $name;
                    $p = $perm;
                    break;
                }
                $c++;
            }
            if ($n == "" or $p =="") return;
            if ($player->hasPermission($p)) {
                Loader::getInstance()->playerData[$player->getName()]["tag"] = $n;
                $player->sendMessage(TF::GREEN."Successfully equipped tag!");
            } else {
                $player->sendMessage(TF::RED."You have not unlocked this tag yet!");
            }
        });
        $form->setTitle("Tags");
        $form->setContent("Pick a tag!");
        $form->addButton("Close Menu");
        foreach ($tags as $name => $permission) {
            if ($player->hasPermission($permission)) {
                $perm = TF::RESET.TF::GREEN."Unlocked";
            } else {
                $perm = TF::RESET.TF::RED."Locked";
            }
            $form->addButton($name."\n$perm");
        }
        $player->sendForm($form);
    }

    public function openVotePoints(Player $player) : void {
        $form = new SimpleForm(function(Player $player, $data = null) {
            switch($data) {
                case 0:
                    $player->sendMessage(TF::GREEN."You have ".Loader::getInstance()->playerData[$player->getName()]["vp"]." vote points.");
                    break;
                case 1:
                    $this->openVotePointShop($player);
                    break;
                default:


            }
        });
        $form->setTitle("Vote Points");
        $form->setContent("Tap on an option");
        $form->addButton("Check Vote Points");
        $form->addButton("Vote Point Shop");
        $form->addButton("Close Menu");
        $player->sendForm($form);
    }

    public function openVotePointShop(Player $player) : void {
        $form = new SimpleForm(function(Player $player, $data = null) {
            $vp = Loader::getInstance()->playerData[$player->getName()]["vp"];
            switch($data) {
                case 0:
                    if ($vp >= 10) {
                        $player->sendMessage(TF::GREEN."Successfully purchased Coal Rank!");
                        Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "setgroup \"".$player->getName()."\" Coal");
                        Loader::getInstance()->playerData[$player->getName()]["vp"] -= 10;
                    } else {
                        $player->sendMessage(TF::RED."You do not have enough vote points to purchase this!");
                    }
                    break;
                case 1:
                    if ($vp >= 20) {
                        $player->sendMessage(TF::GREEN."Successfully purchased Iron Rank!");
                        Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "setgroup \"".$player->getName()."\" Iron");
                        Loader::getInstance()->playerData[$player->getName()]["vp"] -= 20;
                    } else {
                        $player->sendMessage(TF::RED."You do not have enough vote points to purchase this!");
                    }
                    break;
                case 2:
                    if ($vp >= 30) {
                        $player->sendMessage(TF::GREEN."Successfully purchased Gold Rank!");
                        Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "setgroup \"".$player->getName()."\" Gold");
                        Loader::getInstance()->playerData[$player->getName()]["vp"] -= 30;
                    } else {
                        $player->sendMessage(TF::RED."You do not have enough vote points to purchase this!");
                    }
                    break;
                case 3:
                    if ($vp >= 40) {
                        $player->sendMessage(TF::GREEN."Successfully purchased Diamond Rank!");
                        Loader::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "setgroup \"".$player->getName()."\" Diamond");
                        Loader::getInstance()->playerData[$player->getName()]["vp"] -= 40;
                    } else {
                        $player->sendMessage(TF::RED."You do not have enough vote points to purchase this!");
                    }
                    break;
                default:
            }
        });
        $form->setTitle("Vote Points");
        $form->setContent("Tap on an option");
        $form->addButton("Coal Rank\n10 Vote Points");
        $form->addButton("Iron Rank\n20 Vote Points");
        $form->addButton("Gold Rank\n30 Vote Points");
        $form->addButton("Diamond Rank\n40 Vote Points");
        $form->addButton("Close Menu");
        $player->sendForm($form);
    }

    public function openShopForm1(Player $player) : void {
        $form = new SimpleForm(function(Player $player, $data = null) {
            switch ($data) {

                case 0:
                    $this->openShopCategory($player, "Farming");
                    break;

                default:

            }
        });
        $form->setTitle("ZPrisons Shop");
        $form->setContent("Choose a category.");
        $form->addButton("Farming");
        $form->addButton("Close");
        $player->sendForm($form);
    }

    public array $categories = [
        "Farming" => [
            "81:0" => [
                "name" => "Cactus",
                "price" => 50000
            ],
            "12:0" => [
                "name" => "Sand",
                "price" => 5000
            ],
            "9:0" => [
                "name" => "Water",
                "price" => 2500
            ],
            "410:0" => [
                "name" => "Hopper",
                "price" => 5000
            ],
            "174:0" => [
                "name" => "Packed Ice",
                "price" => 2500
            ]
        ]
    ];

    public function openShopCategory(Player $player, string $category) : void {
        $ids = [];
        foreach ($this->categories[$category] as $id => $info) {
            $ids[] = $id;
        }
        $form = new SimpleForm(function(Player $player, $data = null) use ($category, $ids) {
            if (empty($ids[$data])) return;
            if (!isset($this->categories[$category][$ids[$data]])) return;
            $info = $this->categories[$category][$ids[$data]];
            $price = $info["price"] * (float)Loader::getInstance()->playerData[$player->getName()]["multiplier"];
            $this->buyItemForm($player, $ids[$data], $price);
        });
        $form->setTitle($category);
        $form->setContent("Choose a option to buy");
        foreach ($ids as $id) {
            $info = $this->categories[$category][$id];
            $form->addButton("{$info["name"]}\n$".$info["price"]* (float)Loader::getInstance()->playerData[$player->getName()]["multiplier"]);
        }
        $form->addButton("Close");
        $player->sendForm($form);
    }

    public function buyItemForm(Player $player, string $id, int $price) : void {
        $form = new CustomForm(function (Player $player, $data = null) use ($price, $id) : void {
            if($data === null) {
                return;
            }
            if (!is_numeric($data[0])) {
                $player->sendMessage("§cPlease enter a number.");
                return;
            }
            $stripped = explode(":", $id);
            $amount = (int)$data[0];
            $price = $price * $amount;
            $money = Loader::getInstance()->economyAPI->myMoney($player);
            if ($money >= $price) {
                Loader::getInstance()->economyAPI->reduceMoney($player, $price);
                $item = Item::get($stripped[0], $stripped[1], $amount);
                $player->getInventory()->addItem($item);
                $player->sendMessage("§aSuccessfully purchased.");
            } else {
                $player->sendMessage("§cYou do not have enough money to purchase that.");
            }
        });
        $form->setTitle("Buy Form");
        $form->addInput("Amount", "64", "64");
        $player->sendForm($form);
    }

}