<?php
declare(strict_types= 1);

namespace TwitchWatcher;

use InvalidArgumentException;
use TwitchWatcher\Exceptions\ConfigurationException;
class Logger
{
    private const APP_LOG_DEFAULT = "app.log";
    private const ERROR_LOG_DEFAULT = "error.log";

    // public enum LogLevels:
    

    private string $log_dir;
    private ?string $appLog;
    private ?string $errorLog;
    private $appLogFH;
    private bool $verbose = false;
    private bool $debug = false;
    public function __construct(array $config = null)
    {
        if (null !== $config) {
            $this->setConfig($config);
        } else {
            $config = Application::config();
            $this->setConfig($config);
        }

        $this->appLogFH = fopen($this->log_dir . DIRECTORY_SEPARATOR . $this->appLog, 'a');
        // $this->errorLogFH = fopen($this->log_dir . DIRECTORY_SEPARATOR . $this->errorLog, 'a');
    }

    public function setConfig(array $config): void
    {
        if (isset($config['logger'])) {
            $config = $config['logger'];
        }
        if (!isset($config['log_dir'])) {
            throw new ConfigurationException('Не задана директория логов');
        }

        $main = Application::config();
        $this->log_dir = $main['base_dir'] . DIRECTORY_SEPARATOR . $config['log_dir'];

        if (!isset($config['application_log'])) {
            $this->appLog = self::APP_LOG_DEFAULT;
        } else {
            $this->appLog = $config['application_log'];
        }

        if (!isset($config['error_log'])) {
            $this->errorLog = self::ERROR_LOG_DEFAULT;
        } else {
            $this->errorLog = $config['error_log'];
        }
        if (isset($config['verbose'])) {
            $this->verbose = $config['verbose'];
        }
        if (isset($config['debug'])) {
            $this->debug = $config['debug'];
        }
    }

    // Решил не засирать сигнатуры этих шорткат методов параметром $verbose, 
    // надо вербоз - вызывай log
    public function info(string $message): void
    {
        $this->log($message, LogLevel::Info);
    }

    public function warning(string $message): void
    {
        $this->log($message, LogLevel::Warning);
    }

    public function error(string $message): void
    {
        $this->log($message, LogLevel::Error);
    }

    public function debug(string $message): void
    {
        if ($this->debug) {
            $this->log($message, LogLevel::Debug);
        }
    }

    public function log(string $message, LogLevel $level, bool $verbose = false): void
    {
        
        $target = match($level) {
            LogLevel::Info, LogLevel::Debug => $this->appLogFH,
            LogLevel::Warning, LogLevel::Error => $this->appLogFH,
        };
        $label = \strtoupper($level->name);

        $timestamp = (new \DateTime())->format('Y-m-d H:i:s');
        $message = "$timestamp [$label] $message";

        $this->writeToFile($message, $target);

        if ($verbose || $this->verbose) {
            echo $message . PHP_EOL;
        }
    }

    private function writeToFile(string $message, $target): void
    {
        if (!is_resource($target)) {
            throw new InvalidArgumentException('Аргумент $target в методе Logger->write() должен быть указателем на файл');
        }
        
        fwrite($target, $message . PHP_EOL);
    }
}