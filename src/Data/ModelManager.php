<?php

declare(strict_types=1);

namespace TwitchWatcher\Data;

use TwitchWatcher\Models\ModelInterface;

class ModelManager
{
    private string $table, $column, $value;
    public function __construct(private DataManager $dm, private ModelInterface $model)
    {
        
    }

    public function get(string $class): ModelManager
    {
        if (!class_exists($class)) {
            throw new \RuntimeException("Trying to get non-existing class: $class");
        }

        if (!($class instanceof ModelInterface)) {
            throw new \RuntimeException("Argument \$class must be instance of TwitchWatcher\Models\ModelInterface");
        }
        
        $this->model = new $class();
        $this->table = strrchr($class, '\\');
        return $this;
        /*
        return match(true) {
            !is_null($id)   => $em->findById($id, new self),
            !empty($column) => $em->findByColumn($column, new self),
        };
        */
    }

    public function byId(int $id) : ModelManager
    {

        $this->column = 'id';
        $this->value = $id;
        return $this;
    }

    public function byColumn(string $column) : ModelManager
    {
        list($this->column, $this->value) = explode('.', $column);
        return $this;
    }

    public function find($criteria, ModelInterface $model) : ModelInterface
    {

    }

    public function one(): ModelInterface
    {
        $result = $this->dm->select($this->table, $this->column, "{$this->column}={$this->value}");

        $this->model->fill($result);

        return $this->model;
    }

    public function collection(): ModelCollection
    {

    }

    public function save(ModelInterface|ModelCollection $data) : ModelInterface
    {

    }

}