<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Util;

use Traversable;

use function iterator_to_array;

/**
 * @internal
 */
final class Util
{
    /**
     * @param iterable<mixed> $it
     *
     * @return array<mixed> $it
     *
     * @psalm-template  T
     * @psalm-param iterable<T> $it
     * @psalm-return array<T>
     */
    public static function iterableToArray(iterable $it): array
    {
        return $it instanceof Traversable
            ? iterator_to_array($it)
            : $it;
    }
}
