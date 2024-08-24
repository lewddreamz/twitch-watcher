<?php

namespace TwitchWatcher\App;

class Config
{
    public function __construct (private array $options) {}

    public function __get(string $option): mixed {
        if (in_array($option, $this->options)) {
        return $this->options[$option];
        } else {
            throw new \Exception("No such option {$option}");
        }
    }

}