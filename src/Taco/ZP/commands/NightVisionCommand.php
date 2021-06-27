<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;

class NightVisionCommand extends PluginCommand {

    public function __construct(Plugin $owner) {
        parent::__construct("nv", $owner);
        $this->setDescription("Give yourself NightVision");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        $eff = new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 20 * 600, 0);
        $sender->addEffect($eff);
        $sender->sendMessage(TF::GREEN."Successfully turned on night vision.");
        return true;
    }

}