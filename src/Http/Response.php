<?php

namespace Http;

class Response
{
    private const STATUS_TEXTS = [
        200 => "OK",
        201 => "Created",
        204 => "No Content",
        400 => "Bad Request",
        401 => "Unauthorized",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        500 => "Internal Server Error",
    ];

    private int $statusCode;
    private array $headers;
    private string $body;

    public function __construct(int $status = 200)
    {
        $this->setStatus($status);
        $this->headers = [];
        $this->body = "";
    }

    public function setStatus(int $code): self
    {
        if (!array_key_exists($code, self::STATUS_TEXTS)) {
            throw new \InvalidArgumentException(
                "HTTP status code {$code} is invalid.",
            );
        }

        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function send(): void
    {
        $statusText = self::STATUS_TEXTS[$this->statusCode];
        $httpHeader = sprintf("HTTP/1.1 %d %s", $this->statusCode, $statusText);
        header($httpHeader, true, $this->statusCode);

        foreach ($this->headers as $name => $value) {
            $header = sprintf("%s: %s", $name, $value);
            header($header, true);
        }

        echo $this->body;
        exit();
    }
}
