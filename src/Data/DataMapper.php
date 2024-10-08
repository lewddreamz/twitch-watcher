<?php

declare(strict_types=1);

namespace TwitchWatcher\Data;

use TwitchWatcher\Collections\ModelCollection;
use TwitchWatcher\Collections\PersistableCollection;
use TwitchWatcher\Exceptions\NotInitializedException;
use TwitchWatcher\Models\ModelInterface;
use TwitchWatcher\Models\PersistableModel;


class DataMapper
{
    private string $table, $column = '*', $orderStr, $limitStr;
    private mixed $value;
    private Condition $condition;
    private PersistableModel|PersistableCollection $model;
    public function __construct(private DBAL $dm)
    {
        
    }

    public function find(PersistableModel|PersistableCollection $model): self
    {
        
        /*if (is_null($model->id)) {
            throw new \InvalidArgumentException("No id");
        }*/
        $this->table = $model::getTableName();
        if (empty($this->table)) {
            throw new NotInitializedException("Метод " . $model::class ." ->getTableName() не вернул валидное имя таблицы");
        }
        $this->model = $model;
        return $this;
    }
    public function columns(string $columns): static 
    {
        $this->column($columns);
        return $this;
    }
    public function byId(int $id): self
    {

        return $this->where(new Condition("id=$id"));
    }
    public function where(Condition $condition): self
    {
        $this->condition = $condition;
        return $this;
    }

    public function orderAsc(?string $column): self
    {
        $this->orderStr = "ORDER BY $column ASC";
        return $this;
    }
    public function orderDesc(?string $column): self
    {
        $this->orderStr = "ORDER BY $column DESC";
        return $this;
    }
    public function limit(int $limit): self
    {
        $limit = (string)$limit;
        $this->limitStr = "LIMIT $limit";
        return $this;
    }

    /**
     * Вернуть все записи из набора, найденного по запросу
     * @return \TwitchWatcher\Collections\PersistableCollection
     */
    public function all(): PersistableCollection
    {
        #TODO додумать 
        return $this->do();
    }
    
    /**
     * Вернуть первую вставленную запись из набора
     * @return \TwitchWatcher\Models\PersistableModel
     */
    public function first(): PersistableModel
    {
        $this->orderAsc('id')->limit(1);
        return $this->do();
    }

    /**
     * Вернуть последнюю вставленную запись из набора
     * @return \TwitchWatcher\Models\PersistableModel
     */
    public function last(): PersistableModel
    {
        $this->orderDesc('id')->limit(1);
        return $this->do();
    }


    /**
     * Вернуть одну модель
     * @return \TwitchWatcher\Models\PersistableModel
     */
    public function one(): PersistableModel
    {
        #TODO додумать
        $this->limit(1);
        return $this->do();
    }
    #TODO сделать этот метод приватным и вызывать его потом из all, first, last, one
    public function do(): PersistableModel|PersistableCollection
    {
        if (!empty($this->condition)) {
            $condStr = "{$this->condition->leftOperand}{$this->condition->operator}{$this->condition->rightOperand}";
        }
        $result = $this->dm->select($this->table, $this->column, $condStr ?? null, $this->orderStr ?? null, $this->limitStr ?? null);
        if (empty($result)) {
            #TODO нормальное исключение
            #TODO наверно вообще его убрать
            throw new \Exception("No data found in data source");
        }
        //это временно, потому что по хорошему тут должно быть 2 отдельных метода для модели или коллекции, либо два полиморфных класса
        // в любом случае, TODO убрать
        if (count($result) == 1) {
            $result = $result[0];
        }
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
        if (!empty($model->id)) {
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