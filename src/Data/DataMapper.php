<?php

declare(strict_types=1);

namespace TwitchWatcher\Data;

use TwitchWatcher\Collections\ModelCollection;
use TwitchWatcher\Collections\ModelCollectionInterface;
use TwitchWatcher\Models\ModelInterface;
use TwitchWatcher\Models\PersistedModel;

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

    public function insert(PersistedModel|ModelCollectionInterface $values): bool
    {
        if ($values instanceof PersistedModel) {
            $this->insertModel($values);
        } else {
            $this->insertCollection();
        }
    }

    private function insertModel(PersistedModel $model)
    {
        $table = $model->getTableName();
        if ($model->id) {
            if ($this->dm->exists($table, 'id', $model->id)) {
                $this->dm->update($table, $model->getValues(), 'id=' . $model->id);
            }
        }
        $this->dm->insert($model->getTableName(), $model->getValues());
    }

    private function insertCollection(ModelCollectionInterface $collection): bool
    {
        
    }
    public function deleteObject(ModelInterface $model): bool
    {

    }

    public function deleteCollection(ModelCollectionInterface $collection): bool
    {

    }
}