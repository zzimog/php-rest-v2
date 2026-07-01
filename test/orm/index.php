<?php

use Core\DataType;
use ORM\Model;
use ORM\Attributes\Column;
use ORM\Attributes\Primary;
use ORM\Attributes\Table;

require_once "../../src/autoload.php";
require_once "utils.php";

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
  #[Column(foreign: User::class)]
  public int $user;

  #[Column(nullable: true)]
  public string $name;

  #[Column(nullable: true)]
  public string $surname;
}

/**
 * Test
 */

$user = new User();
pre_print("Table name: {$user::getTable()}");
table_print($user::getColumns());

$primaries = $user::getPrimary();
pre_print("Primary key: ", $primaries);

$uniques = $user::getUniques();
pre_print("Unique columns: ", $uniques);

echo "<hr/>";

$info = new UserInfo();
pre_print("Table name: {$info::getTable()}");
table_print($info::getColumns());
