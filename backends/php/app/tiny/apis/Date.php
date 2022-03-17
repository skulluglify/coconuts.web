<?php namespace tiny;

use DateTimeZone;
use DateTime;
use Exception;


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


enum DayType: int
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
    public function getDay(): int;
    public function getDayOfYear(): int;
    public function getWeekday(): int;
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

    public function __construct(mixed $datetime = "now", string $abbr = "CST")
    {

        // initialize Time Zone
        $this->tz = new DateTimeZone(
            timezone: timezone_name_from_abbr($abbr)
        );

        // initialize Time
        try
        {

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
}