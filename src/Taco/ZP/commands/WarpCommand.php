<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class WarpCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("warp", $owner);
        $this->setDescription("Warp to a new destination!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        Loader::getForms()->openWarpForm($sender);
        return true;
    }

}