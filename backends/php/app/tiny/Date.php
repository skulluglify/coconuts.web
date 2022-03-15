<?php namespace app\tiny;

use DateTimeZone;
use DateTime;
use Exception;


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
    private string $tm_month;
    private int $tm_day;
    private int $tm_yDay;
    private int $tm_wDay;

    public function __construct(mixed $datetime = "now", string $abbr = "CST") {

        // initialize Time Zone
        $this->tz = new DateTimeZone(
            timezone: timezone_name_from_abbr($abbr)
        );

        // initialize Time
        try {

            $this->tm = new DateTime(
                datetime: $datetime,
                timezone: $this->tz
            );

            $this->timestamp = $this->tm->getTimestamp();

            // get Location
            $l = $this->tz->getLocation();
            $lt = localtime(
                timestamp: $this->timestamp,
                associative: true
            );

            // fetch data from DateTime
            $this->timezone = $this->tz->getName();
            $this->tz_country_code = $l["country_code"];
            $this->tz_latitude = $l["latitude"];
            $this->tz_longitude = $l["longitude"];
            $this->offset = $this->tm->getOffset() / 3600;
            $this->daylight = $lt["tm_isdst"];
            // ($lt["tm_hour"] + 7) % 24 for 24 Hours, Format H
            // ($lt["tm_hour"] + 7) % 12 for 12 Hours, Format h
            // Cause Any Country have Different way, I will use format
            $this->tm_hour = ($lt["tm_hour"] + $this->offset) % 12;
            $this->tm_hour24 = ($lt["tm_hour"] + $this->offset) % 24;
            // Other short way
            // $this->tm_midnight = $this->tm->format("A");
            if ($this->tm_hour24 < 12) {
                $this->tm_midnight = "AM";
            } else { // max 23, return "AM" if 0 | 24
                $this->tm_midnight = "PM";
            }
            $this->tm_minute = $lt["tm_min"];
            $this->tm_second = $lt["tm_sec"];
            $this->tm_year = $lt["tm_year"] + 1900;
            $this->tm_month = $lt["tm_mon"] + 1; // start at 1
            $this->tm_day = $lt["tm_mday"];
            $this->tm_yDay = $lt["tm_yday"]; // start at 0, 365 days
            $this->tm_wDay = (($lt["tm_wday"] + 6) % 7) + 1; // start at 1, set first is monday

        } catch (Exception $e) {

            // I don't know, how to handle this
            // Sorry
        }

    }

    public function getTimezone(): string {

        return $this->timezone;
    }

    public function getCountryId(): string {

        return $this->tz_country_code;
    }

    public function getLatitude(): float {

        return $this->tz_latitude;
    }

    public function getLongitude(): float {

        return $this->tz_longitude;
    }

    public function getOffset(): int {

        return $this->offset;
    }

    public function getDaylight(): int {

        return $this->daylight;
    }

    public function getTimestamp(): int {

        return $this->timestamp;
    }

    public function getHour(): int {

        return $this->tm_hour24;
    }

    public function getHourOfDST(): int {

        return $this->tm_hour;
    }

    public function getMidnight(): string {

        return $this->tm_midnight;
    }

    public function getMinute(): int {

        return $this->tm_minute;
    }

    public function getSecond(): int {

        return $this->tm_second;
    }

    public function getYear(): int {

        return $this->tm_year;
    }

    public function getMonth(): int {

        return $this->tm_month;
    }

    public function getDay(): int {

        return $this->tm_day;
    }

    public function getDayOfYear(): int {

        return $this->tm_yDay;
    }

    public function getWeekday(): int {

        return $this->tm_wDay;
    }
}