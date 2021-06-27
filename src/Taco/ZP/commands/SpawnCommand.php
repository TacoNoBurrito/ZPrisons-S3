<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class SpawnCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("spawn", $owner);
        $this->setDescription("Teleport to spawn!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        Loader::getUtils()->teleportToSpawn($sender);
        $sender->sendMessage(TF::GREEN."Successfully teleported to spawn.");
        return true;
    }

}