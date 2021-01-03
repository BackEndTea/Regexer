<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

final class UnclosedBracketList extends SyntaxException
{
    public static function create(): self
    {
        return new self('Braketlist "[]", wasn\'t closed');
    }
}
