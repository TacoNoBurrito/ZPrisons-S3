<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class MuteCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("mute", $owner);
        $this->setDescription("Mute a player!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender->hasPermission("core.mute")) {
            if (empty($args[0])) {
                $sender->sendMessage("§cPlease provide a player to mute!");
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
            Loader::getInstance()->punishmentData[$player->getName()]["isMuted"] = true;
            Loader::getInstance()->punishmentData[$player->getName()]["muteReason"] = $reason;
            Loader::getInstance()->getServer()->broadcastMessage("§l§cPUNISHMENT! §r§eType: Mute\n§r§bPunisher: §d".$sender->getName()."\n§r§bPunished: §d".$player->getName()."\n§r§bReason: §d".$reason);
        }
        return true;
    }

}