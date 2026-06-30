<?php

if (! function_exists("array_first")) {
  function array_first(array $array)
  {
    return $array ? $array[array_key_first($array)] : null;
  }
}

function pre_print(mixed ...$any): void
{
  echo "<pre>";
  foreach ($any as $a) {
    print_r($a);
  }
  echo "<pre/>";
}

function table_print(array $columns): void
{
  if (!empty($columns)) {
    echo "<table style='width:100%;border-collapse:collapse;'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th></th>";
    foreach (array_keys(array_first($columns)) as $column) {
      echo "<th style='border:1px solid;'>$column</th>";
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($columns as $name => $props) {
      echo "<tr>";
      echo "<td style='border:1px solid;'>$name</td>";
      foreach ($props as $value) {
        echo "<td style='border:1px solid;'>$value</td>";
      }
      echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
  }
}
