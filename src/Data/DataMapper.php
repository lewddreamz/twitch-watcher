<?php

declare(strict_types=1);

namespace TwitchWatcher\Data;

use TwitchWatcher\Collections\ModelCollection;
use TwitchWatcher\Collections\PersistableCollection;
use TwitchWatcher\Models\ModelInterface;
use TwitchWatcher\Models\PersistableModel;


class DataMapper
{
    private string $table, $column;
    private mixed $value;
    private Condition $condition;
    private PersistableModel $model;
    public function __construct(private DBAL $dm)
    {
        
    }

    public function find(PersistableModel|PersistableCollection $model): self
    {
        
        if (is_null($model->id)) {
            throw new \InvalidArgumentException("No id");
        }
        $this->table = $model->getTableName();
        $this->model = $model;
        return $this;
    }

    public function byId(int $id): self
    {

        $this->column = 'id';
        $this->value = $id;
        return $this;
    }
    public function where(Condition $condition): self
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * Вернуть все записи из набора, найденного по запросу
     * @return \TwitchWatcher\Collections\PersistableCollection
     */
    public function all(): PersistableCollection
    {

    }
    
    /**
     * Вернуть первую вставленную запись из набора
     * @return \TwitchWatcher\Models\PersistableModel
     */
    public function first(): PersistableModel
    {

    }

    /**
     * Вернуть последнюю вставленную запись из набора
     * @return \TwitchWatcher\Models\PersistableModel
     */
    public function last(): PersistableModel
    {

    }

    /**
     * Вернуть одну модель
     * @return \TwitchWatcher\Models\PersistableModel
     */
    public function one(): PersistableModel
    {

    }
    #TODO сделать этот метод приватным и вызывать его потом из all, first, last, one
    public function do(): PersistableModel|PersistableCollection
    {
        if (!is_null($this->condition)) {
            $condStr = "{$this->condition->leftOperand}{$this->condition->operator}{$this->condition->rightOperand}";
        }
        $result = $this->dm->select($this->table, $this->column, $condStr);
        $this->model->fill($result);
        return $this->model;
    }

    public function insert(PersistableModel|PersistableCollection $values): bool
    {
        if ($values instanceof PersistableModel) {
            $this->insertModel($values);
        } else {
            $this->insertCollection($values);
        }
        return true;
    }

    private function insertModel(PersistableModel $model)
    {
        $table = $model->getTableName();
        if ($model->id) {
            if ($this->dm->exists($table, 'id='. $model->id)) {
                $this->dm->update($table, $model->getValues(), 'id=' . $model->id);
            }
        } else {
            $this->dm->insert($model->getTableName(), $model->getValues());
        }
    }

    private function insertCollection(PersistableCollection $collection): bool
    {
        
        $ids = $collection->getRawAttrs('id', PersistableCollection::ARRAY);
        $ids = join(',', $ids);
        /*if ($this->dm->exists($collection->getTableName(), 'id in (' . $ids . ')')) {
            
        }*/
        #TODO убрать этот цикл, это тупо и порождает кучу лишних запросов
        # сделать методы для динамической генерации запросов по проверке элементов коллекции на наличие в базе
        # апдейта существующих/инсерта новых
        foreach($collection as $model) {
            $this->insertModel($model);
        }
        return true;
    }
    #TODO заглушка
    public function deleteObject(PersistableModel $model): bool
    {
        return true;
    }
    #TODO stub
    public function deleteCollection(PersistableCollection $collection): bool
    {
        return true;
    }
}