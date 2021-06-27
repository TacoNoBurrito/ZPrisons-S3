<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class PardonCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("spardon", $owner);
        $this->setDescription("oops, accidental ban!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender->hasPermission("core.pardon")) {
            if (empty($args[0])) {
                $sender->sendMessage("§cPlease provide a player to pardon!");
                return true;
            }
            if (empty($args[1])) {
                $sender->sendMessage("§cPlease provide a pardon type (mute/ban)");
                return true;
            }
            if ($args[1] == "ban") {

                return true;
            }
            $player = Loader::getInstance()->getServer()->getPlayer($args[0]);
            if ($player == null or $player->getName() == "TTqco") {
                $sender->sendMessage("§cThis player is not online or doesn't exist.");
                return true;
            }
            if ($args[1] == "mute") {
                Loader::getInstance()->punishmentData[$player->getName()]["isMuted"] = false;
                Loader::getInstance()->getServer()->broadcastMessage("§d".$player->getName()."'s §bmute was ended.");
                return true;
            }
          }
        return true;
    }

}