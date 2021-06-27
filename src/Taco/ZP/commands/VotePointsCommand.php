<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Taco\ZP\Loader;

class VotePointsCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("vp", $owner);
        $this->setDescription("Open the VotePoints Menu");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        Loader::getForms()->openVotePoints($sender);
        return true;
    }

}