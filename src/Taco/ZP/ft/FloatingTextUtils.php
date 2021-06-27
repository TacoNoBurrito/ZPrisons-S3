<?php namespace Taco\ZP\ft;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use Taco\ZP\Loader;

class FloatingTextUtils {

    public array $fts = [
        "ft1" => [
            "x" => 0,
            "y" => 0,
            "z" => 0,
            "text" => [
                "bruh1",
                "bruh2"
            ]
        ],
        "ft2" => [
            "x"  => 257,
            "y" => 70,
            "z" => 241,
            "text" => [
                "§l§dZ§bPrisons\n§r§bWelcome.",
                "§l§bZ§dPrisons\n§r§bWelcome."
            ]
        ],
        "f54" => [
            "x"  => 229,
            "y" => 71,
            "z" => 170,
            "text" => [
                "§l§cPVP§fMine\n§r§fPvP Is Enabled, Be Careful!",
                "§l§cPVP§fMine\n§r§fPvP Is Enabled, Be Careful!"
            ]
        ]
    ];

    public function register() : void {
        foreach($this->fts as $name => $array) {
            $pos = new Vector3($array["x"], $array["y"], $array["z"]);
            Loader::getInstance()->getServer()->getDefaultLevel()->loadChunk($pos->getX() >> 16, $pos->getZ() >> 16);
            $nbt = Entity::createBaseNBT($pos, null, 0, 0);
            $entity = new FloatingTextEntity(Loader::getInstance()->getServer()->getDefaultLevel(), $nbt, $array["text"]);
            $entity->spawnToAll();
        }
    }

}