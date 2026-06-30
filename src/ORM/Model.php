<?php

namespace ORM;

use Core\DataType;
use ORM\Attributes\Table;
use ORM\Attributes\Column;
use ORM\Attributes\Primary;

class Model
{
  /**
   * Return the model table name. Throws error if table name is not defined.
   *
   * @return string
   *
   * @throws \Error
   */
  public static function getTable(): string
  {
    $reflection = new \ReflectionClass(static::class);
    $attributes = $reflection->getAttributes(Table::class);

    if (!empty($attributes)) {
      $instance = $attributes[0]->newInstance();
      return $instance->name;
    }

    throw new \Error('Table not defined');
  }

  /**
   * Return the model primary key. Returns null if model does not have a primary key.
   *
   * @return ?string
   */
  public static function getPrimary(): ?string
  {
    $reflection = new \ReflectionClass(static::class);
    $props = $reflection->getProperties();

    foreach ($props as $prop) {
      $isColumn = !empty($prop->getAttributes(Column::class));
      $isPrimary = $isColumn && !empty($prop->getAttributes(Primary::class));

      if ($isPrimary) {
        return $prop->getName();
      }
    }

    return null;
  }

  /**
   * Return the model unique keys.
   *
   * @return array<string,string[]>
   */
  public static function getUniques(): array
  {
    $reflection = new \ReflectionClass(static::class);
    $props = $reflection->getProperties();

    return array_reduce($props, function ($uniques, $prop) {
      $attribute = $prop->getAttributes(Column::class);

      if (!empty($attribute)) {
        $column = $attribute[0]->newInstance();
        $key = $column->unique;

        if ($key) {
          $uniques[$key][] = $prop->getName();
        }
      }

      return $uniques;
    }, []);
  }

  /**
   * Return the model columns
   *
   * @return array<string,array<string,mixed>>
   */
  public static function getColumns(): array
  {
    $reflection = new \ReflectionClass(static::class);
    $props = $reflection->getProperties();

    return array_reduce($props, function ($columns, $prop) {
      $attribute = $prop->getAttributes(Column::class);

      if (!empty($attribute)) {
        $name = $prop->getName();
        $column = $attribute[0]->newInstance();
        $isNumber = $column->type === DataType::NUMBER;
        $isPrimary = !empty($prop->getAttributes(Primary::class));
        $isIncremental = $isNumber && ($isPrimary || $column->incremental);

        if ($column->foreign) {
          $Class = $column->foreign;
          $foreignKey = $column->foreignKey ?? $Class::getPrimary();
          $foreignColumn = $Class::getColumns()[$foreignKey];

          $foreignColumn['primary'] = $isPrimary;
          $foreignColumn['incremental'] = false;
          $foreignColumn['foreign'] = $Class;
          $foreignColumn['foreignKey'] = $foreignKey;

          return [...$columns, $name => $foreignColumn];
        }

        return [
          ...$columns,
          $name => [
            'primary'     => $isPrimary,
            'type'        => $column->type->name,
            'size'        => $column->size,
            'decimal'     => $column->decimal,
            'default'     => $column->default,
            'unique'      => $column->unique,
            'incremental' => $isIncremental,
            'foreign'     => $column->foreign,
            'foreignKey'  => $column->foreignKey,
          ]
        ];
      }

      return $columns;
    }, []);
  }
}
