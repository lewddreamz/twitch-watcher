<?php
declare(strict_types=1);

namespace TwitchWatcher;

use Exception;

class DateHelper
{
    public static function normalizeDate(string $dateString)
    {
        $matches = [];
        if (preg_match('/(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})Z/', $dateString, $matches)) {
            return $matches[1] . ' ' . $matches[2];
        } else {
            throw new Exception('Передана дата в неверном формате');
        }
    }
}