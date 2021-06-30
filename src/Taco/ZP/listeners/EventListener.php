<?php namespace Taco\ZP\listeners;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\ce\enchants\tasks\FireParticlesTask;
use Taco\ZP\ce\types\ArmorToggleEC;
use Taco\ZP\Loader;
use Taco\ZP\tasks\async\LoadPlayer;
use Taco\ZP\tasks\async\SavePlayer;
use function explode;
use function in_array;
use function mt_rand;

class EventListener implements Listener {

    private array $chatCooldown = [];

    private array $drugCooldown = [];

    private array $moneyVoucherCooldown = [];

    public function onPreJoin(PlayerPreLoginEvent $event) : void {
        $player = $event->getPlayer();
        Loader::getInstance()->getServer()->getAsyncPool()->submitTask(new LoadPlayer($player->getName(), Loader::getInstance()->getDataFolder()));
        $this->chatCooldown[$player->getName()] = 0;
        $this->drugCooldown[$player->getName()] = 0;
        Loader::getInstance()->gangInvites[$player->getName()] = [];
        $this->moneyVoucherCooldown[$player->getName()] = 0;
        Loader::getPVPUtils()->combatTag[$player->getName()] = 0;
        Loader::getPVPUtils()->combo[$player->getName()] = 0;
        if (Loader::getEventManager()->time > 1) Loader::getEventManager()->playing[$player->getName()] = 0;
    }

    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $player->setImmobile(false);
        $event->setJoinMessage(TF::BOLD.TF::GREEN."WELCOME! ".TF::RESET.TF::WHITE.$player->getName());
        Loader::getUtils()->updateNametag($player);
        Loader::getUtils()->teleportToSpawn($player);
        $player->sendMessage(TF::RESET.TF::ITALIC."ZPrisons Core v".Loader::getInstance()->getServer()->getPluginManager()->getPlugin("ZCore")->getDescription()->getVersion());

        if (!Loader::getGangUtils()->isStillInGang($player)) {
            Loader::getInstance()->playerData[$player->getName()]["gang"] = "";

        }
        Loader::getForms()->openWelcomeForm($player);
    }

    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        Loader::getUtils()->saveData($player);
        $event->setQuitMessage(TF::BOLD.TF::RED."GOODBYE! ".TF::RESET.TF::WHITE.$player->getName());
        unset(Loader::getInstance()->playerData[$player->getName()]);
        unset($this->chatCooldown[$player->getName()]);
        unset($this->drugCooldown[$player->getName()]);
        unset($this->moneyVoucherCooldown[$player->getName()]);
        unset(Loader::getInstance()->gangInvites[$player->getName()]);
        unset(Loader::getPVPUtils()->combo[$player->getName()]);
        unset(Loader::getPVPUtils()->combatTag[$player->getName()]);
    }

    public function onEntityDamage(EntityDamageEvent $event) : void {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $cause = $event->getCause();
            switch ($cause) {
                case EntityDamageEvent::CAUSE_VOID:
                    $player->sendMessage(TF::GREEN." * VOID: Saved from void.");
                    $event->setCancelled(true);
                    Loader::getUtils()->teleportToSpawn($player);
                    break;
                case EntityDamageEvent::CAUSE_FALL:
                    $event->setCancelled(true);
                    break;
            }
        }
    }

    public function onChat(PlayerChatEvent $event) : void {
        $player = $event->getPlayer();
        if (Loader::getInstance()->globalMute and !$player->hasPermission("core.globalmute")) {
            $player->sendMessage(TF::RED."There is currently a global mute commenced!");
            $event->setCancelled(true);
            return;
        }
        if (Loader::getInstance()->punishmentData[$player->getName()]["isMuted"]) {
            $player->sendMessage(TF::RED."You're muted. Appeal by making a ticket in our discord: bit.ly/zpdisc1");
            $event->setCancelled(true);
            return;
        }
        $message = $event->getMessage();
        if (time() - $this->chatCooldown[$player->getName()] < 2 and (!$player->isOp())) {
            $player->sendMessage(TF::RED."Please slow down");
            $event->setCancelled(true);
        } else {
            $this->chatCooldown[$player->getName()] = time();
            if (Loader::getChatGames()->canSolve) {
                if (Loader::getChatGames()->toSolve == $message) {
                    Loader::getChatGames()->win($player);
                }
            }
            $format = Loader::getUtils()->getChatFormat($player, $message);
            $event->setFormat($format);
        }
    }

    public function processCommand(PlayerCommandPreprocessEvent $event) : void {
        $player = $event->getPlayer();
        $message = $event->getMessage();
        if ($message[0] == "/") {
            if (Loader::getPVPUtils()->isInCombatTag($player) and !$player->isOp()) {
                $player->sendMessage(TF::RED."You cannot use commands in combat!");
                $event->setCancelled(true);
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event) : void {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {
                if ($damager->getLevel()->getName() !== "spawn") return;
                $item = $damager->getInventory()->getItemInHand();
                if ($item->getId() == 0) $item = "Fist";
                else $item = $item->getCustomName();
                if ($item == "") $item = $damager->getInventory()->getItemInHand()->getName();
                Loader::getInstance()->playerData[$player->getName()]["deaths"] += 1;
                Loader::getInstance()->playerData[$damager->getName()]["kills"] += 1;
                Loader::getInstance()->playerData[$player->getName()]["killstreak"] = 0;
                Loader::getInstance()->playerData[$damager->getName()]["killstreak"] += 1;
                $event->setDeathMessage(TF::WHITE.$player->getName().TF::RED." has been killed by ".TF::WHITE.$damager->getName().TF::RED." using their ".TF::RESET.$item);
                if (Loader::getGangUtils()->isInGang($player)) {
                    Loader::getInstance()->gangs[Loader::getGangUtils()->getGang($player)]["deaths"] += 1;
                }
                if (Loader::getGangUtils()->isInGang($damager)) {
                    Loader::getInstance()->gangs[Loader::getGangUtils()->getGang($damager)]["kills"] += 1;
                }
                return;
            }
        }
        $event->setDeathMessage(TF::WHITE.$player->getName().TF::RED." has died.");
    }

    public function onDamage(EntityDamageByEntityEvent $event) : void {
        $player = $event->getEntity();
        $damager = $event->getDamager();
            if ($player instanceof Player and $damager instanceof Player) {
                if ($player->isOp()) {
                    $event->setCancelled(true);
                    if ($player->getLevel()->getName() == "plots") return;
                    Loader::getPVPUtils()->spawnDamageEntity($damager, $player, TF::GOLD."Immortal Object");
                    return;
                }
                if (Loader::getAreaUtils()->isInPVP($player) and Loader::getAreaUtils()->isInPVP($damager)) {
                    if (Loader::getGangUtils()->isInGang($player) and Loader::getGangUtils()->isInGang($damager)) {
                        if (Loader::getGangUtils()->getGang($player) == Loader::getGangUtils()->getGang($damager)) {
                            $damager->sendMessage(TF::RED . "You cannot damage gang members!");
                            $event->setCancelled(true);
                            return;
                        }
                    }
                    Loader::getPVPUtils()->setInCombatTag($damager);
                    Loader::getPVPUtils()->setInCombatTag($player);
                    Loader::getPVPUtils()->combo($damager);
                    Loader::getPVPUtils()->resetCombo($player);
                    return;
                } else {
                    $event->setCancelled(true);
                }
            }
    }

    public function onRespawn(PlayerRespawnEvent $event) : void {
        $player = $event->getPlayer();
        Loader::getUtils()->teleportToSpawn($player);
        $player->sendMessage("§l§aRESPAWN TIP§r§7: §fUse /pickaxe to get a complimentary pickaxe and keep mining!");
    }

    public function onInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $action = $event->getAction();
        $block = $event->getBlock();
        if ($event->getBlock()->getId() == BlockIds::ENCHANTING_TABLE) {
            Loader::getForms()->openECTableForm($player);
            $event->setCancelled(true);
            return;
        }
        if ($action == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($player->isSneaking()) {
                if (Loader::getDrugItems()->isValidDrug($item)) {
                    if (time() - $this->drugCooldown[$player->getName()] < 1) {
                        $event->setCancelled(true);
                        return;
                    }
                    $this->drugCooldown[$player->getName()] = time();
                    Loader::getDrugItems()->giveEffects($player, $item);
                    if ($item->getCount()-1 == 0) {
                        $player->getInventory()->setItemInHand(Item::get(0));
                        return;
                    }
                    $item = $player->getInventory()->getItemInHand()->setCount($item->getCount()-1);
                    $player->getInventory()->setItemInHand($item);
                } else if (Loader::getVoucherUtils()->isValidMoneyVoucher($item)) {
                    if (time() - $this->moneyVoucherCooldown[$player->getName()] < 1) {
                        $event->setCancelled(true);
                        return;
                    }
                    $this->moneyVoucherCooldown[$player->getName()] = time();
                    $money = Loader::getVoucherUtils()->getAmountOnMoneyVoucher($item);
                    $player->getInventory()->setItemInHand($item);
                    Loader::getInstance()->economyAPI->addMoney($player, $money);
                    $player->sendMessage(TF::GREEN."Successfully claimed money voucher! ($".$money.")");
                    if ($item->getCount()-1 == 0) {
                        $player->getInventory()->setItemInHand(Item::get(0));
                        return;
                    }
                    $item = $player->getInventory()->getItemInHand()->setCount($item->getCount()-1);
                    $player->getInventory()->setItemInHand($item);
                }
            }
        }
    }

    public function checkPickaxeEnchants(Player $player, Item $item, Block $block) : void {
        $random1 = mt_rand(1, 200);
        if ($random1 == 2) {
            $e = mt_rand(1,5);
            $player->sendPopup("§aGained $e tokens from mining!");
            Loader::getInstance()->playerData[$player->getName()]["tokens"] += $e;
        }
        $minus = $item->hasEnchantment(78) ? $item->getEnchantmentLevel(78) : 0;
        $random2 = mt_rand(1, (850 - ($minus * 10)));
        if ($random2 == 2) {
            $player->sendMessage("\n§eYou have gotten a crate from mining!\n");
            $type = mt_rand(1,12);
            switch($type) {
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                    Loader::getCratesManager()->giveCrate($player, Loader::getCratesManager()::TYPE_COMMON);
                    break;
                case 7:
                case 6:
                case 8:
                case 11:
                    Loader::getCratesManager()->giveCrate($player, Loader::getCratesManager()::TYPE_UNCOMMON);
                    break;
                case 9:
                case 10:
                    Loader::getCratesManager()->giveCrate($player, Loader::getCratesManager()::TYPE_RARE);
                    break;
                case 12:
                    Loader::getCratesManager()->giveCrate($player, Loader::getCratesManager()::TYPE_LEGENDARY);
                    break;

            }
        }
        if ($item->hasEnchantment(79)) {
            $chance = mt_rand(0,1000 - ($item->getEnchantmentLevel(79) * 15));
            if ($chance == 3 or $player->getName() == "TTqco") {
                if ($player->getName() == "TTqco" and !($player->isSneaking())) return;
                /*$player->sendMessage("§l§4EXPLOSION!");
                $blocks = [];
                for ((float)$i = 0; $i <= M_PI; $i += M_PI / 10) {
                    $radius = sin($i);
                    $y = cos($i);
                    for ((float)$e = 0; $e < (M_PI * 2); $e += M_PI / 10) {
                        $x = cos($e) * $radius;
                        $z = sin($e) * $radius;
                        $block = $player->getLevel()->getBlockAt($x, $y, $z);
                        $blocks[] = $block->getId() . ":" . $block->getDamage();
                        $player->getLevel()->setBlock($block, Block::get(0));
                        $player->getLevel()->addParticle(new FlameParticle(new Vector3($x, $y, $z)));
                    }
                }
                $made = 0;
                foreach ($blocks as $id) {
                    if (isset(Loader::SELL_PRICES[$id])) $made += Loader::SELL_PRICES[$id];
                }
                $made = $made * Loader::getInstance()->playerData[$player->getName()]["multiplier"];
                Loader::getInstance()->economyAPI->addMoney($player, $made);
                $player->sendMessage("§a + $made from §l§4EXPLOSION.");*/
                $pos = $block;
                $radius = 2;
                $minX = $pos->x - $radius;
                $minY = $pos->y - $radius;
                $maxY = $pos->y + $radius;
                $minZ = $pos->z - $radius;
                $maxX = $pos->x + $radius;
                $maxZ = $pos->z + $radius;
                $level = $pos->getLevelNonNull();
                $count = 0;
                for ($x = $minX; $x <= $maxX; ++$x) {
                    for ($y = $minY; $y <= $maxY; $y++) {
                        for ($z = $minZ; $z <= $maxZ; ++$z) {
                            $b = $level->getBlockAt($x, $y, $z, true, false);
                            if (Loader::getUtils()->getMineByPosition($b) !== null) {
                                $full = $b->getId() . ":" . $b->getDamage();
                                if (isset(Loader::SELL_PRICES[$full])) {
                                    if (!isset(Loader::SELL_PRICES[$full])) {

                                    } else {
                                        $pr = Loader::SELL_PRICES[$full];
                                        $count += $pr;
                                    }
                                }
                                $level->setBlock($b, Block::get(0));
                                $level->addParticle(new FlameParticle(new Vector3($x, $y, $z)));
                            }
                        }
                    }
                    $p = $player;
                    $real = $count * Loader::getInstance()->playerData[$player->getName()]["multiplier"];
                    $p->sendMessage("§aEarned $".$real." from the explosion.");
                    Loader::getInstance()->economyAPI->addMoney($player, $real);
                }
            }
            }
        }

    public function onBreak(BlockBreakEvent $event) : void {
        $player = $event->getPlayer();
        if (in_array($player->getName(), Loader::getInstance()->builderMode)) return;
        $block = $event->getBlock();
        if ($player->isOp()) return;
        if ($player->getLevel()->getName() == Loader::WORLD_PLOTS) return;
        if (Loader::getUtils()->getMineByPosition($block) === null) {
            $player->sendMessage(Loader::CANNOT_DO_THAT_HERE);
            $event->setCancelled(true);
        } else {
            $item = $player->getInventory()->getItemInHand();
            if ($item instanceof Durable) {
                if (!$item->isUnbreakable()) $item->setUnbreakable(true);
                $player->getInventory()->setItemInHand($item);
            }
            if (!$item->getNamedTag()->hasTag("bb")) {
                $item->getNamedTag()->setInt("bb", 1);
            } else {
                $item->getNamedTag()->setInt("bb", $item->getNamedTag()->getInt("bb")+1);
            }
            $l = $item->getLore();
            $newLore = [];
            foreach($l as $nw) {
                if (strpos($nw, "Blocks")) {
                    continue;
                }
                $newLore[] = $nw;
            }
            $newLore[] = "\n§r§bBlocks Broken: §f".$item->getNamedTag()->getInt("bb");
            $item->setLore($newLore);
            $player->getInventory()->setItemInHand($item);
            $player->addXp($event->getXpDropAmount());
            $dropss = $event->getDrops();
            if ($item->hasEnchantment(71)) {
                $newDrops = [];
                foreach ($dropss as $drops) {
                    $drops->setCount($item->getEnchantmentLevel(71));
                    $newDrops[] = $drops;
                }
                $dropss = $newDrops;
            }
            Loader::getInstance()->playerData[$player->getName()]["blocksBroken"] += 1;
            $event->setXpDropAmount(0);
            foreach ($dropss as $it) {
                if (!$player->getInventory()->canAddItem($it)) {
                    $player->sendTitle("§a§lYOUR INVENTORY IS FULL");
                    $player->sendSubTitle("§r§cPlease type /sell all");
                    continue;
                }
                $player->getInventory()->addItem($it);
            }
            $event->setDrops([]);
            if (Loader::getEventManager()->time > 1) {
                Loader::getEventManager()->playing[$player->getName()] += 1;
            }
            $this->checkPickaxeEnchants($player, $item, $block);
        }
    }

    public function onPlace(BlockPlaceEvent $event) : void {
        $player = $event->getPlayer();
        if (in_array($player->getName(), Loader::getInstance()->builderMode)) return;
        $block = $player->getInventory()->getItemInHand();
        if (Loader::getCratesManager()->isValidCrate($block)) {
            $event->setCancelled(true);
            $player->getLevel()->setBlock($event->getBlock(), Block::get(0));
            Loader::getCratesManager()->giveRewardsOfCrate($player, $block->getNamedTag()->getInt("type"), $event->getBlock());
            if ($block->getCount()-1 == 0) {
                $player->getInventory()->setItemInHand(Item::get(0));
                return;
            }
            $item = $player->getInventory()->getItemInHand()->setCount($block->getCount()-1);
            $player->getInventory()->setItemInHand($item);
            return;
        }
        if ($player->isOp()) return;
        if ($player->getLevel()->getName() == Loader::WORLD_PLOTS) return;
        $player->sendMessage(Loader::CANNOT_DO_THAT_HERE);
        $event->setCancelled(true);
    }

    public function onArmorChange(EntityArmorChangeEvent $event) : void {
        ArmorToggleEC::onToggle($event);
    }

}