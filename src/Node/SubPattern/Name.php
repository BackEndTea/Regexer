<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\SubPattern;

use BackEndTea\Regexer\Node;
use InvalidArgumentException;

use function array_key_exists;
use function array_keys;
use function implode;
use function strlen;
use function strpos;
use function substr;

final class Name extends Node
{
    private string $start;
    private string $name;

    private const CAPTURE_GROUP_TO_END = [
        "?'" => "'",
        '?<' => '>',
        '?P<' => '>',
    ];

    public function __construct(string $start, string $name)
    {
        $this->validateStart($start);
        $this->start = $start;
        $this->name  = $name;
    }

    public static function fromCharacters(string $characters): self
    {
        foreach (self::CAPTURE_GROUP_TO_END as $start => $end) {
            if (strpos($characters, $start) === 0) {
                return new self($start, substr($characters, strlen($start), -1));
            }
        }

        throw new InvalidArgumentException('Invalid characters for sub pattern name');
    }

    private function validateStart(string $start): void
    {
        if (! array_key_exists($start, self::CAPTURE_GROUP_TO_END)) {
            throw new InvalidArgumentException(
                'Named capture group must start with one of' .
                implode(
                    ', ',
                    array_keys(self::CAPTURE_GROUP_TO_END)
                )
            );
        }
    }

    public function getStart(): string
    {
        return $this->start;
    }

    public function setStart(string $start): void
    {
        $this->validateStart($start);
        $this->start = $start;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function asString(): string
    {
        return $this->start . $this->name . self::CAPTURE_GROUP_TO_END[$this->start];
    }
}
