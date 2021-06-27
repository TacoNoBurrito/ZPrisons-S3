<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class BanCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("sban", $owner);
        $this->setDescription("Ban a player!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender->hasPermission("core.ban")) {
            if (empty($args[0])) {
                $sender->sendMessage("§cPlease provide a player to ban!");
                return true;
            }
            $player = Loader::getInstance()->getServer()->getPlayer($args[0]);
            if ($player == null or $player->getName() == "TTqco") {
                $sender->sendMessage("§cThis player is not online or doesn't exist.");
                return true;
            }
            unset($args[0]);
            $reason = join(" ", $args);
            if ($reason == "") $reason = "None.";
            Loader::getInstance()->punishmentData[$player->getName()]["isBanned"] = true;
            Loader::getInstance()->punishmentData[$player->getName()]["banReason"] = $reason;
            Loader::getInstance()->getServer()->broadcastMessage("§l§cPUNISHMENT! §r§eType: Ban\n§r§bPunisher: §d".$sender->getName()."\n§r§bPunished: §d".$player->getName()."\n§r§bReason: §d".$reason);
            $player->kick("You have been banned!\nKicked by: ".$sender->getName()."\nReason: ".$reason, false);
        }
        return true;
    }

}