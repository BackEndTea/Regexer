<?php

declare(strict_types=1);

namespace BackEndTea\Regexer;

abstract class Token
{
    protected function __construct(private string $content)
    {
    }

    public function asString(): string
    {
        return $this->content;
    }
}
