<?php namespace Taco\ZP\leaderboards;

use Taco\ZP\Loader;

class Sorter {

    public function getEconomyLeaderboard() : string {
        $all = Loader::getInstance()->economyAPI->getAllMoney();
        $message = "";
        if(count($all) > 0){
            arsort($all);
            $i = 0;
            foreach($all as $name => $money){
                $message .= "§b$name: §d$".Loader::getUtils()->intToPrefix($money)."\n";
                if($i >= 10) break;
                $i++;
            }
        }
        return "§l§dTop§bMoney\n§r".$message;
    }

    //Figure out why it only works like 30 percent of the time, may be an issue with the entity and not the sorter.
    public function getClanKills() : string {
        $return = "";
        $all = Loader::getInstance()->gangs;
        $asKills = [];
        foreach ($all as $name => $info) {
            $asKills[$name] = $info["kills"];
        }
        arsort($asKills);
        $i = 0;
        foreach ($asKills as $name => $kills) {
            $return .= "§b$name: §d$kills\n";
            if ($i >= 10) break;
            $i++;
        }
        return "§l§dTop§bGangs\n§r$return";
    }

}