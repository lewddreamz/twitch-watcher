<?php
declare(strict_types=1);

namespace TwitchWatcher\Data;

use SQLite3;
use SQLite3Result;

class SQLite3DBAL implements DBAL
{
    private SQLite3 $db;
    private string $filename;

    public function __construct(string $filename) {
        $this->filename = $filename;
        // create sqlite database file if doesnt exists
        if (!is_file($this->filename)) {
            touch($this->filename);
        }
        $this->db = new SQLite3($this->filename);
        $this->db->enableExceptions(true);
        $this->initTables();
    }

    private function initTables(): bool {
        $this->db->exec("CREATE TABLE IF NOT EXISTS 'vods' (
            'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            'name' VARCHAR NOT NULL,
            'description' VARCHAR NOT NULL,
            'uploadDate' DATETIME NOT NULL, 
            'twitch_id' VARCHAR NOT NULL,
            'url' VARCHAR NOT NULL,
            'streamer_id' INTEGER NOT NULL)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS 'streamers' (
            'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            'name' VARCHAR NOT NULL,
            'url' VARCHAR NOT NULL)");
        $this->db->exec("CREATE TABLE IF NOT EXISTS 'notifications' (
            'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            'vod_id' INTEGER NOT NULL,
            'is_notified' BOOLEAN,
            'notification_timestamp' TIMESTAMP)");
        return true;
    }

    public function select(string $table, string $columns, string|array $condition = null, ?string $order = '', ?string $limit = '') : array {
        $query = "SELECT $columns FROM $table";
        if (!is_null($condition)) {
            $query .= " WHERE $condition";
        }
        $query .= " $order $limit";
        $result = $this->db->query($query);
        if ($result instanceof SQLite3Result) {
            $collection = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $collection[] = $row;
            }
            return $collection;
        } else {
            return [];
        }
    }

    public function insert(string $table, array|string $values):bool
    {
        $columns = join(",",array_keys($values));
        array_walk($values, function(&$v) {
            if (is_string($v)) {
            $v = str_replace("'", "''", $v);
        }});
        $values = "'" . join("','", array_values($values)) . "'";
        $sql = "INSERT INTO $table ($columns) values ($values)";
        return $this->db->exec($sql);
    }

    public function update(string $table, array|string $values, array|string $where): bool
    {
        $setArr =  [];
        $set = array_walk($values, function($v, $k) use (&$setArr){ 
            if (is_string($v)) {
                $setArr[]= "$k = '$v'";
            } else {
                $setArr[] = "$k = $v";
            }
        });
        $set = join(',', $setArr);
        $sql = "UPDATE $table SET $set";
        if (!empty($where)) {
            $sql .= " WHERE $where";
        }
        return $this->db->exec($sql);
    }
    public function query(string $sql): array
    {
        $res = $this->db->query($sql);
        if ($res) {
            $collection = [];
            while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
                $collection[] = $row;
            }
            return $collection;
        } else {
            return [];
        };
    }

    /**
     * Вернет скалярное значение из 1 строки результирующего набора
     */
    public function queryScalar($sql): mixed
    {
        $result = $this->db->query($sql);
        $row = $result->fetchArray(SQLITE3_NUM);
        if (!empty($row)) {
            return $row[0];
        } else {
            return false;
        }
    }
    public function saveVod(array $vod): bool {
        return $this->db->exec("INSERT INTO 'vods' ('name', 'description', 'uploadDate', 'url', 'twitch_id', 'streamer_id')
        VALUES ('{$vod['name']}', '{$vod['description']}', '{$vod['uploadDate']}',
        '{$vod['url']}', '{$vod['twitch_id']}', '{$vod['streamer_id']}')");
    }

    public function exists(string $table, array|string $conds): bool {
        $result = $this->db->query("SELECT EXISTS(SELECT * FROM $table WHERE $conds)");
        if ($result) {
            $array = $result->fetchArray(SQLITE3_NUM);
            if ($array[0] == 1) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    #TODO stub method
    public function delete(string $table, array|string $cond): bool
    {
        return true;
    }
}