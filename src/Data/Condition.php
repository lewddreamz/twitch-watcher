<?php

namespace TwitchWatcher\Data;

use DomainException;

/**
 * Represents an arbitrary condition
 */
class Condition
{
    protected string $operator, $leftOperand, $rightOperand;
    public function __construct(private array|string $conds)
    {
        if (is_string($conds)) {
            $pattern = '/(\w+)(>|<|!=|=)(\w+)/';
            $matches = [];
            if (preg_match($pattern, $conds, $matches)) {
                $this->leftOperand = $matches[1];
                $this->setOperator($matches[2]);
                $this->rightOperand = $matches[3];
            } else {
                throw new \InvalidArgumentException("Wrong format of condition string!");
            }
        } else {
            if (count($conds) != 3) {
                throw new \InvalidArgumentException("Wrong conditionals array");
            }
            #TODO Добавиь проверкуу операндов
            $this->leftOperand = $conds[0];
            $this->rightOperand = $conds[1];
            $this->setOperator($conds[2]);
        }
    }

    private function setOperator(string $operator): void
    {
        if (in_array($operator, ['>', '<', '!=', '='])) {
            $this->operator = $operator;
        } else {
            throw new \InvalidArgumentException("Invalid operator $operator, only '>', '<', '!=', '=' are prohibited");
        }
    }

    public function __get($prop)
    {
        if (isset($this->$prop)) {
            return $this->$prop;
        } else {
            throw new DomainException("No such property $prop in class " . self::class . ".")
        }
    }
}