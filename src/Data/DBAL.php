<?php
namespace TwitchWatcher\Data;
interface DBAL
{
    public function select(string $table, string $columns, string|array $conds): array;
    public function update(string $table, string|array $colsVals, array|string $cond): bool;
    public function insert(string $table, string|array $colsVals): bool;
    public function delete(string $table, string|array $cond): bool;
    public function exists(string $table, array|string $conds): bool;
    public function query(string $sql): array;
}