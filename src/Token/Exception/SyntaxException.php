<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Token\Exception;

use BackEndTea\Regexer\RegexerException;
use InvalidArgumentException;

abstract class SyntaxException extends InvalidArgumentException implements RegexerException
{
}
