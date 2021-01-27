<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Node\SubPattern;

use BackEndTea\Regexer\Node;
use InvalidArgumentException;

use function array_key_exists;
use function array_keys;
use function implode;

final class Reference extends Node
{
    private const PREFIX_TO_END = [
        '\\' => '',
        '\g' => '',
        '\g{' => '}',
        '\k{' => '}',
        '\k<' => '>',
        '\k\'' => '\'',
        '(?P=' => ')',
    ];

    private string $referenceTo;
    private string $prefix;

    public function __construct(string $prefix, string $referenceTo)
    {
        if (! array_key_exists($prefix, self::PREFIX_TO_END)) {
            throw new InvalidArgumentException('Prefix must be one of: ' . implode(', ', array_keys(self::PREFIX_TO_END)));
        }

        $this->referenceTo = $referenceTo;
        $this->prefix      = $prefix;
    }

    public function asString(): string
    {
        return $this->prefix . $this->referenceTo . self::PREFIX_TO_END[$this->prefix];
    }
}
