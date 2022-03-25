<?php namespace tiny;

use DateTimeZone;
use DateTime;
use Exception;


// ref https://www.php.net/manual/en/function.localtime.php
enum DateType: int
{

    case TM_SECOND = 0;
    case TM_MINUTE = 1;
    case TM_HOUR = 2;
    case TM_DAY_OF_MONTH = 3;
    case TM_MONTH = 4;
    case TM_YEAR = 5;
    case TM_DAY_OF_WEEK = 6;
    case TM_DAY_OF_YEAR = 7;
    case TM_IS_DAYLIGHT = 8;
}


enum LocationType: string
{
    case COUNTRY_CODE = "country_code";
    case LATITUDE = "latitude";
    case LONGITUDE = "longitude";
}


enum WeekdayType: int
{
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;
    case SUNDAY = 7;
}


enum MonType: int
{
    case JANUARY = 1;
    case FEBRUARY = 2;
    case MARCH = 3;
    case APRIL = 4;
    case MAY = 5;
    case JUNE = 6;
    case JULY = 7;
    case AUGUST = 8;
    case SEPTEMBER = 9;
    case OCTOBER = 10;
    case NOVEMBER = 11;
    case DECEMBER = 12;
}


interface DateStructure
{
    public function getTimezone(): string;
    public function getCountryId(): string;
    public function getLatitude(): float;
    public function getLongitude(): float;
    public function getOffset(): int;
    public function getDaylight(): int;
    public function getTimestamp(): int;
    public function getHour(): int;
    public function getHourOfDST(): int;
    public function getMidnight(): string;
    public function getMinute(): int;
    public function getSecond(): int;
    public function getYear(): int;
    public function getMonth(): int;
    public function getTypeOfMon(): MonType;
    public function getDay(): int;
    public function getDayOfYear(): int;
    public function getWeekday(): int;
    public function getTypeOfWeekday(): WeekdayType;

    // utils
    public static function fix_date(int $Y, int $M, int $D, int $h, int $m, int $s): string;
    public static function enhance_time(int $seconds, int $timestamp, string $abbr): string;
    public static function enhance_time_ms(int $seconds, int $timestamp = 0, string $abbr = "UTC"): int;
}


class Date implements DateStructure
{

    private DateTimeZone $tz;
    private DateTime $tm;
    private string $timezone;
    private string $tz_country_code;
    private float $tz_latitude;
    private float $tz_longitude;
    private int $offset;
    private int $daylight;
    private int $timestamp;
    private int $tm_hour;
    private int $tm_hour24;
    private string $tm_midnight; // AM PM
    private int $tm_minute;
    private int $tm_second;
    private int $tm_year;
    private string $tm_mon;
    private int $tm_day;
    private int $tm_day_of_year;
    private int $tm_day_of_week;

    public function __construct(mixed $datetime = "now", string $abbr = "UTC")
    {

        // initialize Time Zone
        $this->tz = new DateTimeZone(
            timezone: timezone_name_from_abbr($abbr)
        );

        // initialize Time
        try
        {
            if (is_int($datetime)) {

                // auto fixed
                $d = getdate($datetime);

                $Y = $d["year"];
                $M = $d["mon"];
                $D = $d["mday"];
                $h = $d["hours"];
                $m = $d["minutes"];
                $s = $d["seconds"];

                $datetime = "$Y-$M-$D $h:$m:$s";
            }

            $this->tm = new DateTime(
                datetime: $datetime,
                timezone: $this->tz
            );

            $this->timestamp = $this->tm->getTimestamp();

            // get Location
            $l = $this->tz->getLocation();
            $lt = localtime(
                timestamp: $this->timestamp,
                associative: false // no key names, only int key
            );

            // fetch data from DateTime
            $this->timezone = $this->tz->getName();
            $this->tz_country_code = $l[LocationType::COUNTRY_CODE->value];
            $this->tz_latitude = $l[LocationType::LATITUDE->value];
            $this->tz_longitude = $l[LocationType::LONGITUDE->value];
            $this->offset = $this->tm->getOffset() / 3600;
            $this->daylight = $lt[DateType::TM_IS_DAYLIGHT->value];
            // ($lt["tm_hour"] + 7) % 24 for 24 Hours, Format H
            // ($lt["tm_hour"] + 7) % 12 for 12 Hours, Format h
            // Cause Any Country have Different way, I will use format
            $this->tm_hour = ($lt[DateType::TM_HOUR->value] + $this->offset) % 12;
            $this->tm_hour24 = ($lt[DateType::TM_HOUR->value] + $this->offset) % 24;
            // Other short way
            // $this->tm_midnight = $this->tm->format("A");
            if ($this->tm_hour24 < 12)
            {
                $this->tm_midnight = "AM";
            } else
            { // max 23, return "AM" if 0 | 24
                $this->tm_midnight = "PM";
            }
            $this->tm_minute = $lt[DateType::TM_MINUTE->value];
            $this->tm_second = $lt[DateType::TM_SECOND->value];
            $this->tm_year = $lt[DateType::TM_YEAR->value] + 1900;
            $this->tm_mon = $lt[DateType::TM_MONTH->value] + 1; // start at 1, month
            $this->tm_day = $lt[DateType::TM_DAY_OF_MONTH->value]; // start at 1, day of the month
            $this->tm_day_of_year = $lt[DateType::TM_DAY_OF_YEAR->value]; // start at 0, 365 days, I don't have any IDEA
            $this->tm_day_of_week = (($lt[DateType::TM_DAY_OF_WEEK->value] + 6) % 7) + 1; // start at 1, set first is monday

        } catch (Exception $e)
        {

            // I don't know, how to handle this
            // Sorry
            die("Error: ".$e);
        }

    }

    public function getTimezone(): string
    {

        return $this->timezone;
    }

    public function getCountryId(): string
    {

        return $this->tz_country_code;
    }

    public function getLatitude(): float
    {

        return $this->tz_latitude;
    }

    public function getLongitude(): float
    {

        return $this->tz_longitude;
    }

    public function getOffset(): int
    {

        return $this->offset;
    }

    public function getDaylight(): int
    {

        return $this->daylight;
    }

    public function getTimestamp(): int
    {

        return $this->timestamp;
    }

    public function getHour(): int
    {

        return $this->tm_hour24;
    }

    public function getHourOfDST(): int
    {

        return $this->tm_hour;
    }

    public function getMidnight(): string
    {

        return $this->tm_midnight;
    }

    public function getMinute(): int
    {

        return $this->tm_minute;
    }

    public function getSecond(): int
    {

        return $this->tm_second;
    }

    public function getYear(): int
    {

        return $this->tm_year;
    }

    public function getMonth(): int
    {

        return $this->tm_mon;
    }

    public function getTypeOfMon(): MonType
    {
        return match ($this->tm_mon) {
            MonType::JANUARY->value => MonType::JANUARY,
            MonType::FEBRUARY->value => MonType::FEBRUARY,
            MonType::MARCH->value => MonType::MARCH,
            MonType::APRIL->value => MonType::APRIL,
            MonType::MAY->value => MonType::MAY,
            MonType::JUNE->value => MonType::JUNE,
            MonType::JULY->value => MonType::JULY,
            MonType::AUGUST->value => MonType::AUGUST,
            MonType::SEPTEMBER->value => MonType::SEPTEMBER,
            MonType::OCTOBER->value => MonType::OCTOBER,
            MonType::NOVEMBER->value => MonType::NOVEMBER,
            default => MonType::DECEMBER
        };
    }

    public function getDay(): int
    {

        return $this->tm_day;
    }

    public function getDayOfYear(): int
    {

        return $this->tm_day_of_year;
    }

    public function getWeekday(): int
    {

        return $this->tm_day_of_week;
    }

    public function getTypeOfWeekday(): WeekdayType
    {
        return match ($this->tm_day_of_week) {
            WeekdayType::MONDAY->value => WeekdayType::MONDAY,
            WeekdayType::TUESDAY->value => WeekdayType::TUESDAY,
            WeekdayType::WEDNESDAY->value => WeekdayType::WEDNESDAY,
            WeekdayType::THURSDAY->value => WeekdayType::THURSDAY,
            WeekdayType::FRIDAY->value => WeekdayType::FRIDAY,
            WeekdayType::SATURDAY->value => WeekdayType::SATURDAY,
            default => WeekdayType::SUNDAY,
        };
    }

    public static function fix_date(int $Y, int $M, int $D, int $h, int $m, int $s): string
    {

        // fix Y M D h m s
        if (60 <= $s) {

            $l = $s % 60;
            $m += ($s - $l) / 60;
            $s = $l;
        }

        if (60 <= $m) {

            $l = $m % 60;
            $h += ($m - $l) / 60;
            $m = $l;
        }

        // set default 24, 12 with DST
        if (24 <= $h) {

            $l = $h % 24;
            $D += ($h - $l) / 24;
            $h = $l;
        }

        // not like index
        // make precious number
        $K = 12 < $M ? $Y + (($M - ($M % 12)) / 12) : $Y;
        $Z = !($K % 4) ? (($K % 400 and !($K % 100)) ? 28 : 29) : 28;

        if ($Z < $D) {

            $l = $D % $Z;
            $M += ($D - $l) / $Z;
            $D = $l;
        }

        // not like index
        if (12 < $M) {

            $l = $M % 12;
            $Y += ($M - $l) / 12;
            $M = $l;
        }

        $Y = str_pad($Y, 2, "0", STR_PAD_LEFT);
        $M = str_pad($M, 2, "0", STR_PAD_LEFT);
        $D = str_pad($D, 2, "0", STR_PAD_LEFT);
        $h = str_pad($h, 2, "0", STR_PAD_LEFT);
        $m = str_pad($m, 2, "0", STR_PAD_LEFT);
        $s = str_pad($s, 2, "0", STR_PAD_LEFT);

        return "$Y-$M-$D $h:$m:$s";
    }

    public static function enhance_time(int $seconds, int $timestamp = 0, string $abbr = "UTC"): string
    {
        $timestamp = $timestamp > 0 ? $timestamp : "now";

        $start = new self($timestamp, $abbr);

        $seconds = $seconds + $start->getSecond();

        $loose = $seconds % 29030400;
        $years = ($seconds - $loose) / 29030400;
        $years = $years + $start->getYear();
        $seconds = $loose;

        $loose = $seconds % 2419200;
        $months = ($seconds - $loose) / 2419200;
        $months = $months + $start->getMonth();
        $seconds = $loose;

        $loose = $seconds % 86400;
        $days = ($seconds - $loose) / 86400;
        $days = $days + $start->getDay();
        $seconds = $loose;

        $loose = $seconds % 3600;
        $hours = ($seconds - $loose) / 3600;
        $hours = $hours + $start->getHour();
        $seconds = $loose;

        $loose = $seconds % 60;
        $minutes = ($seconds - $loose) / 60;
        $minutes = $minutes + $start->getMinute();
        $seconds = $loose;

        return self::fix_date($years, $months, $days, $hours, $minutes, $seconds);
    }

    public static function enhance_time_ms(int $seconds, int $timestamp = 0, string $abbr = "UTC"): int
    {
        $date = new self(self::enhance_time($seconds, $timestamp, $abbr));
        return $date->getTimestamp();
    }
}
