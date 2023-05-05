<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Util;

use Traversable;

use function array_values;
use function iterator_to_array;

/** @internal */
final class Util
{
    /**
     * @param iterable<T> $it
     *
     * @return list<T>
     *
     * @template  T
     */
    public static function iterableToList(iterable $it): array
    {
        return array_values(
            $it instanceof Traversable
                ? iterator_to_array($it)
                : $it,
        );
    }
}
