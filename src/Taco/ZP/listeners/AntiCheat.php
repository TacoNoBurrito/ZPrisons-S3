<?php namespace Taco\ZP\listeners;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class AntiCheat implements Listener {

    public function onDamage(EntityDamageByEntityEvent $event) : void {
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if ($player instanceof Player) {
            if ($damager instanceof Player) {
                if (Loader::getACUtils()->isReaching($player, $damager, $damager->getPing())) {
                    Loader::getACUtils()->sendMessageToStaff(TF::GOLD."[ANTICHEAT] -> ".TF::GREEN.$damager->getName()." is possibly reaching, please check on them.");
                }
            }
        }
    }

}