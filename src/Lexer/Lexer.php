<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Lexer;

use BackEndTea\Regexer\Stream;
use BackEndTea\Regexer\Token;
use BackEndTea\Regexer\Token\BracketList;
use BackEndTea\Regexer\Token\Delimiter;
use BackEndTea\Regexer\Token\Dot;
use BackEndTea\Regexer\Token\Escaped;
use BackEndTea\Regexer\Token\Exception\InvalidDelimiter;
use BackEndTea\Regexer\Token\Exception\MissingEnd;
use BackEndTea\Regexer\Token\Exception\MissingStart;
use BackEndTea\Regexer\Token\LiteralCharacters;
use BackEndTea\Regexer\Token\Modifier;
use BackEndTea\Regexer\Token\Or_;
use BackEndTea\Regexer\Token\Position;
use BackEndTea\Regexer\Token\Quantifier\QuantifierToken;
use BackEndTea\Regexer\Token\SubPattern;

use function array_key_last;
use function array_pop;
use function assert;
use function is_iterable;
use function preg_match;
use function strlen;
use function substr;

final class Lexer
{
    private ?string $delimiter   = null;
    private int $subPatternCount = 0;
    private bool $hadEnded       = false;

    /**
     * @return iterable<Token>
     */
    public function regexToTokenStream(Stream $input): iterable
    {
        // reset to orignial state
        $this->delimiter       = null;
        $this->subPatternCount = 0;
        $this->hadEnded        = false;

        do {
            if ($this->hadEnded) {
                $modifiers = $input->getUntilEnd();

                yield Modifier::fromModifiers($modifiers);

                break;
            }

            $char = $input->current();

            if ($this->delimiter === null) {
                $this->delimiter = $char;

                yield Delimiter::create($char);

                continue;
            }

            if ($char === $this->delimiter) {
                $this->hadEnded = true;
                $this->assertAllowedToEnd();

                yield Delimiter::create($char);

                continue;
            }

            $literalChars = '';

            do {
                $token = null;
                switch ($char) {
                    case '.':
                        $token = Dot::create();
                        break;
                    case '+':
                        $token = QuantifierToken::plus();
                        break;
                    case '*':
                        $token = QuantifierToken::star();
                        break;
                    case '?':
                        $token = QuantifierToken::questionMark();
                        break;
                    case '^':
                        $token = Position\Start::create();
                        break;
                    case '$':
                        $token = Position\End::create();
                        break;
                    case '(':
                        ++$this->subPatternCount;
                        $token = SubPattern\Start::create();
                        break;
                    case ')':
                        if ($this->subPatternCount === 0) {
                            throw MissingStart::fromEnding('(');
                        }

                        --$this->subPatternCount;
                        $token = SubPattern\End::create();
                        break;
                    case '[':
                        $token = $this->tokensForGroup($input);

                        break;
                    case '{':
                        $token = $this->quantifierFromTokens($input, $input->currentIndex());
                        break;
                    case '|':
                        $token = Or_::create();
                        break;
                    case '\\':
                        $token = Escaped\EscapedCharacter::fromCharacter($input->next());
                        break;
                    case $this->delimiter:
                        $this->hadEnded = true;
                        $token          = Delimiter::create($char);
                        break;
                }

                if ($token !== null) {
                    continue;
                }

                $literalChars .= $char;
                $char          = $input->next();
                if ($char === null) {
                    continue 2;
                }
            } while ($token === null);

            if ($literalChars !== '') {
                yield LiteralCharacters::create($literalChars);
            }

            if (is_iterable($token)) {
                foreach ($token as $item) {
                    assert($item instanceof Token);

                    yield $item;
                }

                continue;
            }

            if ($token) {
                yield $token;

                continue;
            }
        } while ($input->next() !== null);

        if (! $this->hadEnded) {
            throw MissingEnd::fromDelimiter($this->delimiter ?? '');
        }
    }

    private function quantifierFromTokens(Stream $stream, int $currentIndex): ?Token
    {
        $endIndex = $stream->indexOfNext('}', $currentIndex);
        if ($endIndex === null) {
            return null;
        }

        $characters = $stream->getBetween($currentIndex, $endIndex);
        preg_match('/({\d+})|({\d+,\d*})/', $characters, $matches);

        if ($matches === []) {
            return null;
        }

        $stream->moveTo($endIndex);

        return QuantifierToken::fromBracketNotation($characters);
    }

    /**
     * @return iterable<Token>
     */
    private function tokensForGroup(Stream $input): iterable
    {
        // Quick check, it may be escaped, but we don't know yet
        $potentialClosingIndex = $input->indexOfNext(']', $input->currentIndex());
        if ($potentialClosingIndex === null) {
            throw MissingEnd::fromOpening('[');
        }

        // skip the first [
        $input->next();

        $result     = [BracketList\Start::create()];
        $first      = true;
        $characters = '';
        do {
            $current = $input->current();

            switch ($current) {
                case '^':
                    if ($first) {
                        $first    = false;
                        $result[] = BracketList\Not::create();
                        break;
                    }

                    $characters .= $current;
                    break;
                case '-':
                    if ($first) {
                        $first       = false;
                        $characters .= $current;
                        break;
                    }

                    $last = $result[array_key_last($result)];
                    if (
                        $characters === ''
                        && $last instanceof Escaped\EscapedCharacter
                    ) {
                        array_pop($result);
                        $one = $last->asString();
                    } else {
                        $one = substr($characters, -1);
                    }

                    $next = $input->next();
                    if ($next === '\\') {
                        $next .= $input->next();
                    }

                    if (strlen($characters) > 1) {
                        $result[] = BracketList\OneOf::create(substr($characters, 0, -1));
                    }

                    $characters = '';

                    $result[] = BracketList\Range::fromCharacters($one . '-' . $next);
                    break;
                case ']':
                    if ($characters !== '') {
                        $result[] = BracketList\OneOf::create($characters);
                    }

                    $result[] = BracketList\End::create();
                    break 2;
                case '\\':
                    $first = false;
                    if ($characters !== '') {
                        $result[]   = BracketList\OneOf::create($characters);
                        $characters = '';
                    }

                    $next = $input->next();

                    if ($next === null) {
                        throw MissingEnd::fromDelimiter($this->delimiter ?? '');
                    }

                    $result[] = Escaped\EscapedCharacter::fromCharacter($next);
                    break;
                default:
                    if ($current === $this->delimiter) {
                        throw InvalidDelimiter::insideOfGroupAtPosition($input->currentIndex());
                    }

                    $first       = false;
                    $characters .= $current;
            }
        } while ($input->next() !== null);

        if (! $result[array_key_last($result)] instanceof BracketList\End) {
            throw MissingEnd::fromOpening('[');
        }

        foreach ($result as $item) {
            yield $item;
        }
    }

    private function assertAllowedToEnd(): void
    {
        if ($this->subPatternCount !== 0) {
            throw MissingEnd::fromOpening('(');
        }
    }
}
