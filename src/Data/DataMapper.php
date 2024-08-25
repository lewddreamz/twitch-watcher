<?php

declare(strict_types=1);

namespace TwitchWatcher\Data;

use TwitchWatcher\Collections\ModelCollection;
use TwitchWatcher\Models\ModelInterface;

class DataMapper
{
    private string $table, $column;
    private mixed $value;
    public function __construct(private DBAL $dm)
    {
        
    }

    public function find(string $table): self
    {
        
        $this->table = $table;
        return $this;
        /*
        return match(true) {
            !is_null($id)   => $em->findById($id, new self),
            !empty($column) => $em->findByColumn($column, new self),
        };
        */
    }

    public function byId(int $id): self
    {

        $this->column = 'id';
        $this->value = $id;
        return $this;
    }

    public function byColumn(string $column): self
    {
        list($this->column, $this->value) = explode('.', $column);
        return $this;
    }

    public function one(ModelInterface $model): ModelInterface
    {
        $result = $this->dm->select($this->table, $this->column, "{$this->column}={$this->value}");
        $model->fill($result);
        return $model;
    }

    public function collection(ModelCollectionInterface $modelCollection): ModelCollectionInterface
    {
        $res = $this->dm->select($this->table, $this->column, "{$this->column}={$this->value}");
        $modelCollection->fill($res);
        return $modelCollection;
    }

    public function insert(ModelInterface|ModelCollectionInterface $values): bool
    {

    }

    public function deleteObject(ModelInterface $model): bool
    {

    }

    public function deleteCollection(ModelCollectionInterface $collection): bool
    {

    }
}