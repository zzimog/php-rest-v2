<?php

namespace ORM\Attributes;

use Core\DataType;

#[\Attribute]
class Column
{
  public function __construct(
    public DataType $type = DataType::TEXT,
    public int $size = 0,
    public int $decimal = 0,
    public mixed $default = null,
    public ?string $unique = null,
    public ?bool $nullable = false,
    public ?bool $incremental = false,
  ) {
    if ($size === 0) {
      $this->size = match ($type) {
        DataType::BOOLEAN => 1,
        DataType::NUMBER => 10,
        DataType::TEXT => 255
      };
    }
  }
}
