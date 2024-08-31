<?php

namespace TwitchWatcher\Data\DAO;

use TwitchWatcher\Data\DataMapper;

abstract class AbstractDAO

{
    public function __construct(protected DataMapper $dm) {}
}