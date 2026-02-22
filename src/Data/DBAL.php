<?php

namespace TwitchWatcher\Data;

// TODO добавить методы where(), andWhere(), orderBy()...
interface DBAL
{
    public function select(string $table, string $columns, array|string $conds = '', ?string $orderBy = '', ?string $limit = ''): array;

    public function update(string $table, array|string $colsVals, array|string $cond): bool;

    public function insert(string $table, array|string $colsVals): bool;

    public function delete(string $table, array|string $cond): bool;

    public function exists(string $table, array|string $conds): bool;

    public function query(string $sql): array;
}
