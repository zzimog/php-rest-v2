<?php

use Http\JsonResponse;
use SQL\Database;
use SQL\Types\Engine;

require "../src/autoload.php";
require "../config.php";

function sendResponse(array $data, ?int $code = 200)
{
  $response = new JsonResponse($code);
  $response->setData($data);
  $response->send();
}

try {
  $db = new Database(Engine::PGSQL, DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASW);
  $q = $db->query("SELECT * FROM users");
  $c = $db->query("SELECT count(*) AS c FROM users")[0]['c'];

  sendResponse([
    "count" => $c,
    "results" => $q,
  ]);
} catch (Throwable $e) {
  sendResponse([
    "error" => true,
    "message" => $e->getMessage(),
    "code" => $e->getCode(),
    "trace" => $e->getTrace(),
  ], 500);
}
