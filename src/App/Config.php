<?php

namespace TwitchWatcher\App;

class Config
{
    public function __construct (private array $options)
    {
        if (isset($this->options['logger'])) {
            $this->options['logger'] = new Config($options['logger']);
        }
    }

    public function __get(string $option): mixed {
        if (key_exists($option, $this->options)) {
        return $this->options[$option];
        } else {
            throw new \Exception("No such option {$option}");
        }
    }

    public function has(string $key): bool {
        return key_exists($key, $this->options);
    }
}