<?php namespace Taco\ZP\ce;

use pocketmine\item\enchantment\Enchantment;
use Taco\ZP\ce\enchants\CratesPlusEnchantment;
use Taco\ZP\ce\enchants\ExplosionEnchant;
use Taco\ZP\ce\enchants\FireEnchant;
use Taco\ZP\ce\enchants\FlingEnchant;
use Taco\ZP\ce\enchants\FortuneEnchant;
use Taco\ZP\ce\enchants\GearsEnchant;
use Taco\ZP\ce\enchants\GlowingEnchant;
use Taco\ZP\ce\enchants\HasteEnchantment;
use Taco\ZP\ce\enchants\SpeedEnchantment;
use Taco\ZP\ce\enchants\WitherEnchant;

class CEManager {

    //The base of this file was created by prim69 and boomyourbang. Small changed made for ZPrisons Core

    public const SLOT_PICKAXE = 0x400;
    const SLOT_SWORD = 0x10;
    const SLOT_FEET = 0x8;
    const SLOT_LEGS = 0x4;
    const SLOT_TORSO = 0x2;
    const SLOT_HEAD = 0x1;

    public const RARITY_COMMON = 10;
    public const RARITY_UNCOMMON = 5;
    public const RARITY_RARE = 2;
    public const RARITY_MYTHIC = 1;

    public const CONVERSIONS = [
        "Speed" => 70,
        "Fortune" => 71,
        "Haste" => 72,
        "Glowing" => 73,
        "Gears" => 74,
        "Wither" => 75,
        "Fling" => 76,
        "Fire" => 77,
        "Crates+" => 78,
        "Squared" => 79
    ];

    public static function init() : void {
        Enchantment::registerEnchantment(new SpeedEnchantment(70,"Speed",self::RARITY_COMMON, self::SLOT_PICKAXE, 0x0,2));
        Enchantment::registerEnchantment(new FortuneEnchant(71,"Fortune",self::RARITY_RARE, self::SLOT_PICKAXE, 0x0,10));
        Enchantment::registerEnchantment(new HasteEnchantment(72,"Haste",self::RARITY_UNCOMMON, self::SLOT_PICKAXE, 0x0,10));
        Enchantment::registerEnchantment(new GlowingEnchant(73,"Glowing",self::RARITY_COMMON, self::SLOT_HEAD, 0x0,1));
        Enchantment::registerEnchantment(new GearsEnchant(74,"Gears",self::RARITY_COMMON, self::SLOT_FEET, 0x0,2));
        Enchantment::registerEnchantment(new WitherEnchant(75, "Wither", self::RARITY_UNCOMMON, self::SLOT_SWORD, 0x0, 5));
        Enchantment::registerEnchantment(new FlingEnchant(76, "Fling", self::RARITY_RARE, self::SLOT_SWORD, 0x0, 3));
        Enchantment::registerEnchantment(new FireEnchant(77, "Fire", self::RARITY_MYTHIC, self::SLOT_SWORD, 0x0, 1));
        Enchantment::registerEnchantment(new CratesPlusEnchantment(78, "Crates+", self::RARITY_RARE, self::SLOT_PICKAXE, 0x0, 3));
        Enchantment::registerEnchantment(new ExplosionEnchant(79, "Squared", self::RARITY_MYTHIC, self::SLOT_PICKAXE, 0x0, 10));
    }

}