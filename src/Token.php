<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

abstract class Token
{
    private string $content;

    protected function __construct(string $content)
    {
        $this->content = $content;
    }

    public function asString(): string
    {
        return $this->content;
    }
}
