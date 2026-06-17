<?php

namespace Http;

use Http\Response;

class JsonResponse extends Response
{
    protected array $data;

    public function __construct(int $status = 200)
    {
        parent::__construct($status);
        $this->data = [];
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function setValue(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function deleteValue(string $key): self
    {
        unset($this->data[$key]);
        return $this;
    }

    #[\Override]
    public function send(): void
    {
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $json = json_encode($this->data, $flags);

        parent::setHeader("Content-Type", "application/json; charset=utf-8");
        parent::setBody($json);
        parent::send();
    }
}
