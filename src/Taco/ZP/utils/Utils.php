<?php namespace Taco\ZP\utils;

use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;
use Taco\ZP\tasks\async\SavePlayer;
use Taco\ZP\tasks\AutoSaveTask;

class Utils {

    public function teleportToSpawn(Player $player) : void {
        $player->teleport(Loader::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
        $player->teleport(new Vector3(257, 73, 255));
    }

    public function intToPrefix($input) : string {
        if (!\is_numeric($input)) return "0";
        $suffixes = array('', 'K', 'M', 'B', 'T');
        $suffixIndex = 0;
        while(abs($input) >= 1000 && $suffixIndex < sizeof($suffixes)) {
            $suffixIndex++;
            $input /= 1000;
        }
        return (
            $input > 0
                ? floor($input * 1000) / 1000
                : ceil($input * 1000) / 1000
            )
            . $suffixes[$suffixIndex];
    }

    public function n2l(int $num){
        return chr(substr("000".($num+65),-3));
    }

    public function getMaxRankup(string $rank, int $money, int $prestige) : string {
        if ($prestige == 0) $prestige = 1;
        $price = 0;
        $count = $this->l2n($rank);
        while ($money >= $price) {
            $count++;
            if ($this->n2l($count) == "Z") {
                $price += $this->getPriceOfRank($this->n2l($count), $prestige);
                break;
            }
            $p =  $this->getPriceOfRank($this->n2l($count), $prestige);
            if (($p + $price) >= $money) {
                break;
            }
            $price += $this->getPriceOfRank($this->n2l($count), $prestige);
        }
        return $this->n2l($count).":".$price;
    }

    public function getPriceOfRank(string $rank, int $prestige) : int {
        if ($prestige == 0) $prestige = 1;
        $n = $this->l2n($rank) == 0 ? 1 : $this->l2n($rank);
        return abs((($n + 7) * 250 + pow(floor($n * 2 / 2), 1)) * $prestige);
    }

    public function l2n(string $letters){
        $alphabet = range('A', 'Z');
        $number = 0;
        foreach(str_split(strrev($letters)) as $key=>$char){
            $number = $number + (array_search($char,$alphabet))*pow(count($alphabet),$key);
        }
        return $number;
    }

    public function getMineByPosition(Position $position) {
        foreach(Loader::getInstance()->mineReset->getMineManager()->getMines() as $mine){
            if($mine->isPointInside($position)) return $mine;
        }
        return null;
    }

    public function getPurePermsGroup(Player $player) : string {
        return Loader::getInstance()->purePerms->getUserDataMgr()->getGroup($player)->getName();
    }

    public function updateNametag(Player $player) : void {
        $player->setNameTag($this->getNameTag($player));
    }

    public static function intToString(int $int) : string {
        $m = floor($int / 60);
        $s = floor($int % 60);
        return (($m < 10 ? "0" : "").$m.":".((float)$s < 10 ? "0" : "").(float)$s);
    }

    public function getNameTag(Player $player) : string {
        $rank = $this->getFormattedRank($this->getPurePermsGroup($player));
        if (Loader::getGangUtils()->isInGang($player)) {
            $gang = TF::GOLD."*".TF::WHITE.Loader::getGangUtils()->getGang($player)." ";
        } else {
            $gang = "";
        }
        return $gang.$rank.TF::RESET.TF::WHITE." ".$player->getName();
    }

    public function getChatFormat(Player $player, string $message) : string {
        $rank = $this->getFormattedRank($this->getPurePermsGroup($player));
        $tag = Loader::getInstance()->playerData[$player->getName()]["tag"];
        if (!$tag == "") $tag = TF::BOLD.TF::GRAY."[".$tag.TF::RESET.TF::BOLD.TF::GRAY."] ".TF::RESET;
        $prisonRank = Loader::getInstance()->playerData[$player->getName()]["rank"];
        $prestige = Loader::getInstance()->playerData[$player->getName()]["prestige"];
        if (Loader::getGangUtils()->isInGang($player)) {
            $gang = TF::GOLD."*".TF::WHITE.Loader::getGangUtils()->getGang($player)." ";
        } else {
            $gang = "";
        }
        return $gang.TF::RESET.TF::BOLD.TF::GRAY."[".TF::RESET.TF::WHITE.$prisonRank.TF::BOLD.TF::GRAY."] [".TF::WHITE.TF::BOLD."P".$prestige.TF::GRAY."] [".TF::RESET.$rank.TF::BOLD.TF::GRAY."] ".$tag.TF::RESET.TF::WHITE.$player->getName()." » ".TF::GRAY.$message;
    }

    public function getFormattedRank(string $rank) : string {
        $array = [
            "Guest" => TF::GRAY . "Noob",
            "Coal" => TF::BLACK . TF::BOLD . "Coal",
            "Iron" => TF::WHITE . TF::BOLD . "Iron",
            "Gold" => TF::GOLD . TF::BOLD . "Gold",
            "Diamond" => TF::BOLD . TF::AQUA . "Diamond",
            "Helper" => TF::BOLD . TF::GREEN . "Helper",
            "Mod" => TF::BOLD . TF::LIGHT_PURPLE . "Mod",
            "Admin" => TF::BOLD . TF::RED . "Admin",
            "Developer" => TF::BOLD . TF::RED . "Dev",
            "Youtube" => TF::BOLD.TF::WHITE."You".TF::RED."Tube"
        ];
        if (!isset($array[$rank])) return $array["Guest"];
        return $array[$rank];
    }

    public function decodeData($data){
        $data = unserialize(base64_decode(zlib_decode(hex2bin($data))));
        if(is_array($data)) return $data;
        return zlib_decode($data);
    }

    public function encodeData($data){
        return bin2hex(zlib_encode(base64_encode(serialize($data)), ZLIB_ENCODING_DEFLATE, 1));
    }

    public function spawnLightning(Position $pos) : void {
        $light = new AddActorPacket();
        $light->type = "minecraft:lightning_bolt";
        $light->entityRuntimeId = Entity::$entityCount++;
        $light->metadata = [];
        $light->motion = null;
        $light->position = new Vector3($pos->getX(), $pos->getY(), $pos->getZ());
        $light->pitch = 10;
        $light->yaw = 10;
        Loader::getInstance()->getServer()->broadcastPacket($pos->getLevel()->getPlayers(), $light);
    }

    public function saveData(Player $player) : void {
        $info = Loader::getInstance()->playerData[$player->getName()];
        $punish = Loader::getInstance()->punishmentData[$player->getName()];
        Loader::getInstance()->getServer()->getAsyncPool()->submitTask(new SavePlayer($player->getName(), $info["kills"], $info["deaths"], $info["killstreak"], $info["rank"], $info["prestige"], $info["tokens"], $info["multiplier"], Loader::getInstance()->getDataFolder(), $info["tag"], $info["gang"], $punish["isMuted"], $punish["muteReason"], $punish["isBanned"], $punish["banReason"], $info["vp"], $info["blocksBroken"]));
    }



}