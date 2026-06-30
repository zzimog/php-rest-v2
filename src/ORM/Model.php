<?php

namespace ORM;

use ORM\Attributes\Table;
use ORM\Attributes\Column;
use ORM\Attributes\Primary;

class Model
{
  /**
   * Return the model table name
   *
   * @return string The table name
   *
   * @throws \Error Throws error if table name is not defined
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
   * Return the model primary keys
   *
   * @return string[] The primary keys
   */
  public static function getPrimary(): array
  {
    $reflection = new \ReflectionClass(static::class);
    $props = $reflection->getProperties();

    return array_reduce($props, function ($keys, $prop) {
      $attribute = $prop->getAttributes(Column::class);

      if (!empty($attribute)) {
        $name = $prop->getName();
        $isPrimary = !empty($prop->getAttributes(Primary::class));

        return $isPrimary ? [...$keys, $name] : $keys;
      }

      return $keys;
    }, []);
  }

  /**
   * Return the model unique keys
   *
   * @return array<string,string[]> The unique keys
   */
  public static function getUniques(): array
  {
    $reflection = new \ReflectionClass(static::class);
    $props = $reflection->getProperties();

    return array_reduce($props, function ($uniques, $prop) {
      $attribute = $prop->getAttributes(Column::class);

      if (!empty($attribute)) {
        $column = $attribute[0]->newInstance();
        $unique = $column->unique;

        if ($unique) {
          $uniques[$unique][] = $prop->getName();
        }
      }

      return $uniques;
    }, []);
  }

  public static function getColumns(): array
  {
    $reflection = new \ReflectionClass(static::class);
    $props = $reflection->getProperties();

    return array_reduce($props, function ($columns, $prop) {
      $attribute = $prop->getAttributes(Column::class);

      if (!empty($attribute)) {
        $name = $prop->getName();
        $column = $attribute[0]->newInstance();
        $isPrimary = !empty($prop->getAttributes(Primary::class));
        $isIncremental = $isPrimary || $column->incremental;

        return [
          ...$columns,
          $name => [
            'primary' => $isPrimary,
            'type' => $column->type->name,
            'size' => $column->size,
            'decimal' => $column->decimal,
            'default' => $column->default,
            'unique' => $column->unique,
            'incremental' => $isIncremental,
          ]
        ];
      }

      return $columns;
    }, []);
  }
}
