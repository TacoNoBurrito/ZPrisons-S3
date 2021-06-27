<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class GangCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("gangs", $owner);
        $this->setDescription("Gangs Base Command");
        $this->setAliases(["gang"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender instanceof Player) {
            if (empty($args[0])) {
                $sender->sendMessage($this->getUsage());
                return true;
            }
            switch(strtolower($args[0])) {
                case "invite":
                    if (Loader::getGangUtils()->isInGang($sender)) {
                        if (Loader::getGangUtils()->isGangLeader($sender)) {
                            if (empty($args[1])) {
                                $sender->sendMessage(TF::RED."Please provide a player to invite!");
                                return true;
                            }
                            $player = Loader::getInstance()->getServer()->getPlayer($args[1]);
                            if ($player == null) {
                                $sender->sendMessage(TF::RED."This player is not online or doesn't exist.");
                                return true;
                            }
                            if ($player->getName() == $sender->getName()) {
                                $sender->sendMessage(TF::RED."You cannot invite yourself to your gang!");
                                return true;
                            }
                            if (Loader::getGangUtils()->alreadyHasInviteFrom($player, Loader::getGangUtils()->getGang($sender))) {
                                $sender->sendMessage(TF::RED."This player already has an invite from this gang!");
                                return true;
                            }
                            Loader::getGangUtils()->sendInvite($player, Loader::getGangUtils()->getGang($sender));
                            $sender->sendMessage(TF::GREEN."Successfully sent invite to ".$sender->getName()."!");
                        } else {
                            $sender->sendMessage(TF::RED."Only gang leaders can do this!");
                        }
                    } else {
                        $sender->sendMessage(TF::RED."You must be in a gang to do this!");
                    }
                    break;
                case "accept":
                    if (Loader::getGangUtils()->isInGang($sender)) {
                        $sender->sendMessage(TF::RED."You are already in a gang!");
                        return true;
                    }
                    if (empty($args[1])) {
                        $sender->sendMessage(TF::RED."Please provide a gang to join.");
                        return true;
                    }
                    if (!in_array($args[1], Loader::getInstance()->gangInvites[$sender->getName()])) {
                        $sender->sendMessage(TF::RED."You do not have a invite from here!");
                        return true;
                    }
                    Loader::getGangUtils()->acceptInvite($sender, $args[1]);
                    $sender->sendMessage(TF::GREEN."Successfully accepted invite!");
                    Loader::getInstance()->gangInvites[$sender->getName()] = [];
                    break;
                case "create":
                    if (empty($args[1])) {
                        $sender->sendMessage(TF::RED."You must provide a gang name!");
                        return true;
                    }
                    Loader::getGangUtils()->createGang($sender, $args[1]);
                    break;
                case "leave":
                    if (Loader::getGangUtils()->isInGang($sender)) {
                        if (Loader::getGangUtils()->isGangLeader($sender)) {
                            $sender->sendMessage(TF::RED."Gang leaders must disband their gang instead of leaving it!");
                            return true;
                        }
                        Loader::getGangUtils()->kickPlayerFromGang($sender->getName(), Loader::getGangUtils()->getGang($sender));
                        $sender->sendMessage(TF::RED."Successfully left gang.");
                    } else {
                        $sender->sendMessage(TF::RED."You must be in a gang to do this!");
                    }
                    break;
                case "disband":
                    if (Loader::getGangUtils()->isInGang($sender)) {
                        if (Loader::getGangUtils()->isGangLeader($sender)) {
                            Loader::getGangUtils()->disbandGang(Loader::getGangUtils()->getGang($sender));
                            $sender->sendMessage(TF::GREEN."Successfully disbanded gang!");
                        } else {
                            $sender->sendMessage(TF::RED."Only gang leaders can do this!");
                        }
                    } else {
                        $sender->sendMessage(TF::RED."You must be in a gang to do this!");
                    }
                    break;
                case "info":
                    if (empty($args[1])) {
                        $sender->sendMessage(TF::RED."You must provide a gang to get info from!");
                    } else {
                        Loader::getGangUtils()->sendInfoOnGang($sender, $args[1]);
                    }
                    break;
                case "kick":
                    if (Loader::getGangUtils()->isInGang($sender)) {
                        if (Loader::getGangUtils()->isGangLeader($sender)) {
                            if (empty($args[1])) {
                                $sender->sendMessage(TF::RED."You need to provide a player to kick!");
                            } else {
                                if (Loader::getGangUtils()->offlineIsInGang($args[1], Loader::getGangUtils()->getGang($sender))) {
                                    Loader::getGangUtils()->kickPlayerFromGang(trim($args[1]), Loader::getGangUtils()->getGang($sender));
                                } else {
                                    $sender->sendMessage(TF::RED."This player is not in your gang!");
                                }
                            }
                        } else {
                            $sender->sendMessage(TF::RED."Only gang leaders can do this!");
                        }
                    } else {
                        $sender->sendMessage(TF::RED."You must be in a gang to do this!");
                    }
                    break;
                default:
                    $sender->sendMessage($this->getUsage());
            }
        }
        return true;
    }

    public function getUsage(): string
    {
        $arr = [
            "Gangs Help:",
            "/gangs create (name) - Creates a gang",
            "/gangs leave - Leaves your current gang",
            "/gangs disband - disbands your current gang",
            "/gangs kick (name) - kick someone from the gang",
            "/gangs info (name) - Get info on a gang",
            "/gangs invite (name) - Invite someone to the gang",
            "/gangs accept (name) - Join a gang"
        ];
        $m = "";
        foreach ($arr as $a) {
            $m .= $a."\n";
        }
        return $m;
    }

}