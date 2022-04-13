<?php namespace tiny;


use JetBrains\PhpStorm\Pure;

interface StringBaseStructure
{
    public static function ascii_lowercase(): string;
    public static function ascii_uppercase(): string;
    public static function ascii_letters(): string;
    public static function digits(): string;
    public static function alpha(): string;
    public static function hex_digits(): string;
    public static function oct_digits(): string;
    public static function punctuation(): string;
    public static function whitespace(): string;
}


class StringBase implements StringBaseStructure
{

    public static function ascii_lowercase(): string
    {
        return "abcdefghijklmnopqrstuvwxyz";
    }

    public static function ascii_uppercase(): string
    {
        return "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }

    #[Pure] public static function ascii_letters(): string
    {

        return self::ascii_lowercase().self::ascii_uppercase();
    }

    public static function digits(): string
    {

        return "0123456789";
    }

    #[Pure] public static function alpha(): string
    {
        return self::ascii_letters().self::digits();
    }

    #[Pure] public static function hex_digits(): string
    {
        return self::digits().substr(self::ascii_uppercase(), 0, 6);
    }

    #[Pure] public static function oct_digits(): string
    {
        return substr(self::digits(), 0, 8);
    }

    public static function punctuation(): string
    {
        return "!\"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~";
    }

    public static function whitespace(): string
    {
        return " \t\n\r\x0b\x0c";
    }
}