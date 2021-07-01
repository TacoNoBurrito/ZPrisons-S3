<?php namespace Taco\ZP\ft;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use Taco\ZP\Loader;

class FloatingTextUtils {

    public array $fts = [
        "ft1" => [
            "x" => 317,
            "y" => 132,
            "z" => 355,
            "text" => [
                "§l§dBLACKMARKET",
                "§l§fBLACKMARKET"
            ]
        ],
        "ft2" => [
            "x"  => 317,
            "y" => 132,
            "z" => 345,
            "text" => [
                "§l§fPVP",
                "§l§cPVP"
            ]
        ],
        "f54" => [
            "x"  => 322,
            "y" => 132,
            "z" => 350,
            "text" => [
                "§l§eENCHANTING",
                "§l§fENCHANTING"
            ]
        ],
        "lf5" => [
            "x" => 312,
            "y" => 132,
            "z" => 350,
            "text" => [
                "§l§6CHILL",
                "§l§fCHILL"
            ]
        ]
    ];

    public function register() : void {
        foreach($this->fts as $name => $array) {
            $pos = new Vector3($array["x"], $array["y"], $array["z"]);
            Loader::getInstance()->getServer()->getDefaultLevel()->loadChunk($pos->getX() >> 16, $pos->getZ() >> 16);
            $nbt = Entity::createBaseNBT($pos->add(0.5, 0, 0.5), null, 0, 0);
            $entity = new FloatingTextEntity(Loader::getInstance()->getServer()->getDefaultLevel(), $nbt, $array["text"]);
            $entity->spawnToAll();
        }
    }

}