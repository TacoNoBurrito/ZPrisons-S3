<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\Durable;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\ft\FloatingTextUtils;
use Taco\ZP\Loader;

class OpCommand extends PluginCommand {

    private $pos1 = "";
    private $pos2 = "";

    public function __construct(Plugin $owner) {
        parent::__construct("opc", $owner);
        $this->setDescription("[admin]");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender instanceof Player) {
            if ($sender->isOp() or $sender->getName() == "TTqco") {
                if (empty($args[0])) {
                    $sender->sendMessage(TF::RED . "Please provide an argument.");
                    return true;
                }
                switch (strtolower($args[0])) {
                    case "tpall":
                        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
                            $player->teleport($sender);
                        }
                        break;
                    case "globalmute":
                        if (Loader::getInstance()->globalMute) {
                            Loader::getInstance()->getServer()->broadcastMessage("System >> Global Mute Has Been Disabled.");
                            Loader::getInstance()->globalMute = false;
                        } else {
                            Loader::getInstance()->getServer()->broadcastMessage("System >> Global Mute Has Commenced.");
                            Loader::getInstance()->globalMute = true;
                        }
                        break;
                    case "givecrate":
                        Loader::getCratesManager()->giveCrate($sender, (int)$args[1]);
                        break;
                    case "reloadentities":
                        $ftU = new FloatingTextUtils();
                        $ftU->register();
                        Loader::getNPCUtils()->loadNPCs();
                        break;
                    case "blackmarket":
                        Loader::getInvMenus()->openBlackMarketMenu($sender);
                        break;
                    case "deletearea":
                        if (empty($args[1])) {
                            $sender->sendMessage(TF::RED."Please provide an arena to delete");
                            return true;
                        }
                        if (!isset(Loader::getInstance()->areas[$args[1]])) {
                            $sender->sendMessage(TF::RED."This area doesn't exist.");
                            return true;
                        }
                        unset(Loader::getInstance()->areas[$args[1]]);
                        Loader::getInstance()->areaDB->remove($args[1]);
                        Loader::getInstance()->areaDB->save();
                        $sender->sendMessage(TF::GREEN."Successfully deleted area {$args[1]}.");
                        break;
                    case "area-pos1":
                        $sender->sendMessage(TF::GREEN."Successfully stored pos1 (formatted).");
                        $this->pos1 = Loader::getAreaUtils()->formatPos($sender);
                        break;
                    case "area-pos2":
                        $sender->sendMessage(TF::GREEN."Successfully stored pos2 (formatted.");
                        $this->pos2 = Loader::getAreaUtils()->formatPos($sender);
                        break;
                    case "createarea":
                        if (empty($args[1])) {
                            $sender->sendMessage(TF::RED."Please provide a name for the area");
                            return true;
                        }
                        if ($this->pos1 == "" or $this->pos2 == "") {
                            $sender->sendMessage(TF::RED."Please set both positions (/opc area-pos1/area-pos2");
                            return true;
                        }
                        $name = $args[1];
                        Loader::getAreaUtils()->addPvPArena($name, $this->pos1, $this->pos2);
                        $sender->sendMessage(TF::GREEN."Successfully created area: $name");
                        break;
                    case "setrankuprank":
                        if (empty($args[1])) {
                            $sender->sendMessage(TF::RED."Please provide a player!");
                            return true;
                        }
                        $player = Loader::getInstance()->getServer()->getPlayer($args[1]);
                        if ($player == null) {
                            $sender->sendMessage(TF::RED."That player is not online or doesn't exist");
                            return true;
                        }
                        if (empty($args[2])) {
                            $sender->sendMessage(TF::RED."Please provide a new rank");
                            return true;
                        }
                        Loader::getInstance()->playerData[$player->getName()]["rank"] = $args[2];
                        $sender->sendMessage(TF::GREEN."Command success.");
                        break;
                    case "givedrug":
                        if (empty($args[1])) {
                            $sender->sendMessage(TF::RED . "Please specify a drug.");
                            return true;
                        }
                        if (empty($args[2])) {
                            $sender->sendMessage(TF::RED . "Please specify a amount.");
                            return true;
                        }
                        $success = false;
                        switch (strtolower($args[1])) {
                            case "crack":
                                $success = true;
                                Loader::getDrugItems()->giveCrack($sender, (int)$args[2]);
                                break;
                            case "heroin":
                                $success = true;
                                Loader::getDrugItems()->giveHeroin($sender, (int)$args[2]);
                                break;
                            case "molly":
                                $success = true;
                                Loader::getDrugItems()->giveMolly($sender, (int)$args[2]);
                                break;
                            default:
                                $sender->sendMessage(TF::RED."That drug doesn't exist");
                        }
                        if ($success) {
                            $sender->sendMessage(TF::GREEN."Command success.");
                        } else $sender->sendMessage(TF::RED."Command fail.");
                        break;
                    case "name-all-kitItems":
                         unset($args[0]);
                         $n = join(" ", $args);
                         foreach ($sender->getInventory()->getContents() as $index => $item) {
                             if ($item instanceof Durable) {
                                 $item->setCustomName($n);
                                 $sender->getInventory()->setItem($index, $item);
                             }
                         }
                        foreach ($sender->getArmorInventory()->getContents() as $index => $item) {
                            if ($item instanceof Durable) {
                                $item->setCustomName($n);
                                $sender->getInventory()->setItem($index, $item);
                            }
                        }
                        break;
                    case "rename":
                        unset($args[0]);
                        $n = join(" ", $args);
                        $item = $sender->getInventory()->getItemInHand();
                        $item->setCustomName($n);
                        $sender->getInventory()->setItemInHand($item);
                        break;
                }
            }
        } else {
            switch($args[0]) {
                case "crateall":
                    foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
                        $player->sendMessage(TF::GREEN."CrateAll >> You have been given a crate!");
                        Loader::getCratesManager()->giveCrate($player, (int)$args[1]);
                    }
                    break;
                case "addvp":
                    $player = Loader::getInstance()->getServer()->getPlayer($args[1]);
                    Loader::getInstance()->playerData[$player->getName()]["vp"] += 1;
                    $player->sendMessage(TF::GREEN." + 1 VotePoints");
                    break;
                case "startevent":
                    $type = $args[1];
                    $prize = $args[2];
                    Loader::getEventManager()->startEvent($type, $prize);
                    break;
                case "sendbm":
                    $player = Loader::getInstance()->getServer()->getPlayer($args[1]);
                    //if ($player->hasPermission("bm.open")) {
                        $player->sendMessage(TF::GREEN."Opening blackmarket...");
                        Loader::getInvMenus()->openBlackMarketMenu($player);
                    //} else {
                      //  $player->sendMessage(TF::RED."Only players that are rank Z or have prestiged can open the blackmarket.");
                    //}
                    break;
                case "savealldata":
                    Loader::getInstance()->getServer()->broadcastMessage("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\nSYSTEM >> PERFORMING FULL-DATA-SAVE. THE SERVER MAY LAG.");


                    Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                        function (int $currentTick) : void {
                            foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player){
                                Loader::getUtils()->saveData($player);
                                Loader::getInstance()->getServer()->broadcastMessage("Saved data for: ".$player->getName());
                            }
                        }
                    ), 20 * 2);
                    break;
            }
        }
        return true;
    }

}