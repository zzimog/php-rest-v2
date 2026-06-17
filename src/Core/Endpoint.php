<?php

namespace Core;

use Http\JsonResponse;

class Endpoint
{
    private string $message;

    public function __construct(string $message = "")
    {
        $this->message = $message;
    }

    public function __invoke()
    {
        $resp = new JsonResponse();
        $resp->setValue("message", $this->message);
        $resp->send();
    }
}
