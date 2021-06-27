<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class ReportCommand extends PluginCommand {

    private array $times = [];

    public function __construct(Plugin $owner) {
        parent::__construct("report", $owner);
        $this->setDescription("Report a player!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        if (isset($this->times[$sender->getName()])) {
            if ($this->times[$sender->getName()] > 10) {
                $sender->kick("You have sent too many reports", false);
                $this->times[$sender->getName()] = 0;
                return true;
            }
        } else $this->times[$sender->getName()] = 1;
        if (empty($args[0])) {
            $sender->sendMessage("§cPlease provide a player to report!");
            return true;
        }
        $player = Loader::getInstance()->getServer()->getPlayer($args[0]);
        if ($player == null) {
            $sender->sendMessage("§cThis player is not online or doesn't exist!");
            return true;
        }
        unset($args[0]);
        $reason = join(" ", $args);
        if ($reason == "") $reason = "None.";
        Loader::getACUtils()->sendMessageToStaff("§l§cREPORT §r§7>> §b".$sender->getName()." has reported ".$player->getName()."\n§r§eReason: §f".$reason);
        Loader::getACUtils()->sendReport("New Report!\nReporter: ".$sender->getName()."\nReported: ".$player->getName()."\nProvided reason: ".$reason);
        return true;
    }

}