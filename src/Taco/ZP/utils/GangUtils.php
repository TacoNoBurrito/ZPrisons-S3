<?php namespace Taco\ZP\utils;

use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Taco\ZP\Loader;

class GangUtils {

    public function createGang(Player $creator, string $name) : void {
        if ($this->isInGang($creator)) {
            $creator->sendMessage(TF::RED."You are already in a gang!");
            return;
        }
        if (strlen($name) > 2 and strlen($name) < 12 and ctype_alnum($name)) {
            if ($this->gangExists($name)) {
                $creator->sendMessage(TF::RED."A gang with this name already exists!");
                return;
            }
            Loader::getInstance()->gangs[$name] = [
                "leader" => $creator->getName(),
                "members" => "",
                "kills" => 0,
                "deaths" => 0
            ];
            Loader::getInstance()->playerData[$creator->getName()]["gang"] = $name;
            $creator->sendMessage(TF::GREEN."Successfully created gang!");
        } else {
            $creator->sendMessage(TF::RED."Your gang name must be greater than 2 characters, less than 12 characters, and can only use real letters an numbers!");
        }
    }

    public function isStillInGang(Player $player) : bool {
        $gang = $this->getGang($player);
        if (!isset(Loader::getInstance()->gangs[$gang])) return false;
        if (!Loader::getInstance()->gangDB->exists($gang)) return false;
        if ($this->isGangLeader($player)) return true;
        if (Loader::getInstance()->gangs[$gang]["members"] == "") return false;
        $m = Loader::getUtils()->decodeData(Loader::getInstance()->gangs[$gang]["members"]);
        if (in_array($player->getName(), $m)) return true;
        return false;
    }

    public function disbandGang(string $gang) : void {
        $leader = $this->getGangLeader($gang);
        $members = $this->getAllMembersInGang($gang);
        foreach(Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if (in_array($player->getName(), $members)) {
                $player->sendMessage(TF::RED."Your gang was disbanded.");
                Loader::getInstance()->playerData[$player->getName()]["gang"] = "";
            }
            if ($player->getName() == $leader) {
                $player->sendMessage(TF::RED."Your gang was disbanded.");
                Loader::getInstance()->playerData[$player->getName()]["gang"] = "";
            }
        }
        if (isset(Loader::getInstance()->gangs[$gang])) unset(Loader::getInstance()->gangs[$gang]);
        Loader::getInstance()->gangDB->remove($gang);
        Loader::getInstance()->gangDB->save();
    }

    public function alreadyHasInviteFrom(Player $player, string $gang) : bool {
        return in_array($gang, Loader::getInstance()->gangInvites[$player->getName()]);
    }

    public function acceptInvite(Player $player, string $gang) : void {
        $mem = Loader::getInstance()->gangs[$gang]["members"];
        if ($mem == "") {
            $nm = [$player->getName()];
        } else {
            $nm = Loader::getUtils()->decodeData(Loader::getInstance()->gangs[$gang]["members"]);
            $nm[] = $player->getName();
        }
        Loader::getInstance()->gangs[$gang]["members"] = Loader::getUtils()->encodeData($nm);
        Loader::getInstance()->playerData[$player->getName()]["gang"] = $gang;
    }

    public function sendInvite(Player $player, string $gang) : void {
        $player->sendMessage(TF::GREEN."You have recieved a invite from $gang! Please run /gang accept $gang to join!");
        Loader::getInstance()->gangInvites[$player->getName()][] = $gang;
    }

    public function offlineIsInGang(string $player, string $gang) : bool {
        $m = Loader::getUtils()->decodeData(Loader::getInstance()->gangs[$gang]["members"]);
        return in_array($player, $m);
    }

    public function kickPlayerFromGang(string $player, string $gang) : void {
        $m = Loader::getUtils()->decodeData(Loader::getInstance()->gangs[$gang]["members"]);
        unset($m[$player]);
        Loader::getInstance()->gangs[$gang]["members"] = Loader::getUtils()->encodeData($m);
        if (isset(Loader::getInstance()->playerData[$player])) Loader::getInstance()->playerData[$player]["gang"] = "";
    }

    public function getGang(Player $player) : string {
        return Loader::getInstance()->playerData[$player->getName()]["gang"];
    }

    public function gangExists(string $gang) : bool {
        return isset(Loader::getInstance()->gangs[$gang]);
    }

    public function getGangLeader(string $gang) : string {
        return Loader::getInstance()->gangs[$gang]["leader"];
    }

    public function isGangLeader(Player $player) : bool {
        return Loader::getInstance()->gangs[$this->getGang($player)]["leader"] == $player->getName();
    }

    public function isInGang(Player $player) : bool {
        return !$this->getGang($player) == "";
    }

    public function getKillsAndDeaths(string $gang) : array {
        $g = Loader::getInstance()->gangs[$gang];
        return [$g["kills"], $g["deaths"]];
    }

    public function getAllMembersInGang(string $gang) : array {
        if (Loader::getInstance()->gangs[$gang]["members"] == "") return [];
        if (is_array(Loader::getInstance()->gangs[$gang]["members"])) return [];
        return Loader::getUtils()->decodeData(Loader::getInstance()->gangs[$gang]["members"]);
    }

    public function sendInfoOnGang(Player $player, string $gang) : void {
        if (!$this->gangExists($gang)) {
            $player->sendMessage(TF::RED."That gang doesn't exist!");
            return;
        }
        $members = $this->getAllMembersInGang($gang);
        $ms = "";
        foreach ($members as $m) {
            $ms .= $m." ";
        }
        $leader = $this->getGangLeader($gang);
        $kd = $this->getKillsAndDeaths($gang);
        $player->sendMessage(TF::GOLD.$gang." : \n".TF::RESET."Leader: ".TF::YELLOW.$leader."\n".TF::RESET."Members: ".TF::YELLOW."[".$ms."]\n".TF::RESET."K/D: ".TF::YELLOW.$kd[0]."/".$kd[1]);
    }

}