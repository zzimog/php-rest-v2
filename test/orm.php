<?php

use Core\DataType;
use ORM\Model;
use ORM\Attributes\Column;
use ORM\Attributes\Foreign;
use ORM\Attributes\Primary;
use ORM\Attributes\Table;

require_once "../src/autoload.php";

function pre_print(mixed ...$any)
{
  echo "<pre>";
  foreach ($any as $a) {
    print_r($a);
  }
  echo "<pre/>";
}

/**
 * User definition
 */

#[Table('users')]
class User extends Model
{
  #[Primary]
  #[Column(DataType::NUMBER)]
  public int $id;

  #[Column(size: 50, unique: 'username')]
  public string $username;

  #[Column]
  public string $hash;

  #[Column(nullable: true)]
  public string $email;

  #[Column(DataType::BOOLEAN, default: false)]
  public string $active;
}

#[Table('users_info')]
class UserInfo extends Model
{
  #[Primary]
  #[Foreign()]
  public int $user;
}

/**
 * Test
 */

$u = new User();
pre_print("Table name: {$u->getTable()}");

$cols = $u->getColumns();
pre_print("Columns: ", $cols);

$primaries = $u->getPrimary();
pre_print("Primary keys: ", $primaries);

$uniques = $u->getUniques();
pre_print("Unique columns: ", $uniques);

echo "<hr/>";

$info = new UserInfo();
$infoColumns = $info->getColumns();
pre_print("Columns: ", $infoColumns);
