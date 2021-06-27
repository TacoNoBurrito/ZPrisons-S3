<?php namespace Taco\ZP\utils;

use CortexPE\DiscordWebhookAPI\Webhook;
use http\Message;
use pocketmine\math\Vector3;
use pocketmine\math\VoxelRayTrace;
use Taco\ZP\Loader;

class ACUtils {

    public function isReaching(Vector3 $a, Vector3 $p, $ping) : bool {
        foreach(VoxelRayTrace::betweenPoints($a, $p) as $m) {
            $new = $m->distance($p) + $ping * 0.002;
            return $new >= 6.2;
        }
        return false;
    }

    public function sendMessageToStaff(string $message) : void {
        $array = ["Helper", "Mod", "Admin", "Owner"];
        foreach(Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if (in_array(Loader::getUtils()->getPurePermsGroup($player), $array) or $player->isOp()) {
                $player->sendMessage($message);
            }
        }
    }

    public function sendReport(string $msg) : void {
        $webhook = new Webhook(Loader::WEBHOOK_REPORT);
        $message = new \CortexPE\DiscordWebhookAPI\Message();
        $message->setUsername("ZReports");
        $message->setContent($msg);
        $webhook->send($message);
    }

}