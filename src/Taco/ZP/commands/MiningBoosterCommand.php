<?php namespace Taco\ZP\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;
use Taco\ZP\utils\Utils;

class MiningBoosterCommand extends PluginCommand {

    private array $cooldown = [];

    public function __construct(Plugin $owner) {
        parent::__construct("boost", $owner);
        $this->setDescription("Boost your mining!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$sender instanceof Player) return true;
        $time = [
            "Guest" => 20 * 600,
            "Coal" => 20 * 800,
            "Iron" => 20 * 1000,
            "Gold" => 20 * 1200,
            "Diamond" => 20 * 1800,
            "Developer" => 20,
            "Mod" => 20 * 1200,
            "Youtube" => 20 * 1200,
            "Helper" => 20 * 1200
        ];
        $haste = [
            "Guest" => 7,
            "Iron" => 8,
            "Coal" => 9,
            "Gold" => 10,
            "Diamond" => 11,
            "Developer" => 12,
            "Mod" => 10,
            "Youtube" => 10,
            "Admin" => 10,
            "Helper" => 10
        ];
        if (isset($this->cooldown[$sender->getName()]) and time() - $this->cooldown[$sender->getName()] < (20 * 60)) {
            $t = time() - $this->cooldown[$sender->getName()];
            $t = (20 * 60) - $t;
            $sender->sendMessage(TF::RED."Your mining-boost is still on cooldown for ".Utils::intToString($t)."!");
        } else {
            $this->cooldown[$sender->getName()] = time();
            $rank = Loader::getUtils()->getPurePermsGroup($sender);
            $hst = $haste[$rank];
            $time = $time[$rank];
            $eff = new EffectInstance(Effect::getEffect(Effect::HASTE), $time, $hst);
            $sender->addEffect($eff);
            $sender->sendMessage(TF::GREEN."Boost activated!");
        }
        return true;
    }

}