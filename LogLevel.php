<?php
declare(strict_types= 1);

namespace TwitchWatcher;

enum LogLevel: int
{
    case Info = 8;
    case Warning = 1;
    case Error = 2;
    case Debug = 4;
}