<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\BracketList;

use BackEndTea\Regexer\Node;

final class Range extends Node
{
    public function __construct(
        private string $from,
        private string $to,
    ) {
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function setTo(string $to): void
    {
        $this->to = $to;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    public function asString(): string
    {
        return $this->from . '-' . $this->to;
    }
}
