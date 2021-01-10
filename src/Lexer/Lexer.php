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
use BackEndTea\Regexer\Token\Exception\UnclosedBracketList;
use BackEndTea\Regexer\Token\LiteralCharacters;
use BackEndTea\Regexer\Token\Modifier;
use BackEndTea\Regexer\Token\Or_;
use BackEndTea\Regexer\Token\Quantifier\QuantifierToken;
use BackEndTea\Regexer\Token\SubPattern;

use function array_key_last;
use function array_pop;
use function assert;
use function ctype_alnum;
use function ctype_digit;
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
                    case $this->delimiter:
                        $this->hadEnded = true;
                        $token          = Delimiter::create($char);
                        break;
                    case '+':
                    case '*':
                    case '?':
                        $token = QuantifierToken::fromCharacters($char);
                        if ($this->delimiter !== '?' && $input->at($input->currentIndex() + 1) === '?') {
                            $token = [$token, Token\Quantifier\Lazy::create()];
                            $input->next();
                        }

                        break;
                    case '{':
                        $token = $this->quantifierFromTokens($input, $input->currentIndex());
                        if ($token && $this->delimiter !== '?' && $input->at($input->currentIndex() + 1) === '?') {
                            $token = [$token, Token\Quantifier\Lazy::create()];
                            $input->next();
                        }

                        break;
                    case '.':
                        $token = Dot::create();
                        break;
                    case '^':
                        $token = Token\Anchor\Start::create();
                        break;
                    case '$':
                        $token = Token\Anchor\End::create();
                        break;
                    case '(':
                        ++$this->subPatternCount;
                        $token        = SubPattern\Start::create();
                        $currentIndex = $input->currentIndex();
                        if ($this->delimiter === '?') {
                            break;
                        }

                        if ($input->at($currentIndex + 1) === '?') {
                            $token = $this->checkForSpecialSubPattern($token, $input, $currentIndex);
                        }

                        break;
                    case ')':
                        if ($this->subPatternCount === 0) {
                            throw MissingStart::fromEnding('(');
                        }

                        --$this->subPatternCount;
                        $token = SubPattern\End::create();
                        break;
                    case '[':
                        $token = $this->tokensForBracketList($input);

                        break;
                    case '|':
                        $token = Or_::create();
                        break;
                    case '\\':
                        $next = $input->next();

                        if ($next === null) {
                            throw MissingEnd::fromDelimiter($this->delimiter);
                        }

                        $token = $this->createEscapeSequence($input);
                        break;
                }

                if ($token === null) {
                    $literalChars .= $char;
                    $char          = $input->next();
                    if ($char === null) {
                        break;
                    }
                }
            } while ($token === null);

            if ($literalChars !== '') {
                yield LiteralCharacters::create($literalChars);
            }

            if (is_iterable($token)) {
                foreach ($token as $item) {
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
            throw MissingEnd::fromDelimiter($this->delimiter);
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

        return QuantifierToken::fromCharacters($characters);
    }

    /**
     * @return iterable<Token>
     */
    private function tokensForBracketList(Stream $input): iterable
    {
        // Quick check, it may be escaped, but we don't know yet
        $potentialClosingIndex = $input->indexOfNext(']', $input->currentIndex());
        if ($potentialClosingIndex === null) {
            throw UnclosedBracketList::create();
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
                        $first      = false;
                        $characters = $current;
                        break;
                    }

                    $last = $result[array_key_last($result)];
                    if ($characters !== '') {
                        $one = substr($characters, -1);
                    } elseif ($last instanceof Escaped\EscapedCharacter) {
                        array_pop($result);
                        $one = $last->asString();
                    } else {
                        $characters = $current;
                        break;
                    }

                    $next = $input->next();
                    assert($next !== null);
                    if ($next === '\\') {
                        $after = $input->next();
                        assert($after !== null);
                        $next .= $after;
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
                        throw UnclosedBracketList::create();
                    }

                    $result[] = Escaped\EscapedCharacter::fromCharacter($next);
                    break;
                default:
                    if ($current === $this->delimiter) {
                        throw InvalidDelimiter::insideOfBracketListAtPosition($input->currentIndex());
                    }

                    $first       = false;
                    $characters .= $current;
            }
        } while ($input->next() !== null);

        if (! $result[array_key_last($result)] instanceof BracketList\End) {
            throw UnclosedBracketList::create();
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

    /**
     * @return array<Token>
     */
    private function checkForSpecialSubPattern(SubPattern\Start $token, Stream $input, int $currentIndex): array
    {
        $afterQuestionMark = $input->at($currentIndex + 2);
        if ($afterQuestionMark === ':') {
            $input->moveTo($currentIndex + 2);

            return [$token, SubPattern\NonCapturing::create()];
        }

        if ($afterQuestionMark === "'") {
            $closing = "'";
            $start   = $currentIndex + 3;
        } elseif ($afterQuestionMark === '<') {
            $closing = '>';
            $start   = $currentIndex + 3;
        } elseif ($afterQuestionMark === 'P' && $input->at($currentIndex + 3) === '<') {
            $afterQuestionMark = 'P<';
            $closing           = '>';
            $start             = $currentIndex + 4;
        } else {
            throw Token\Exception\InvalidSubPattern::forIncompleteGroupStructure();
        }

        $input->moveTo($start);

        $firstChar = true;

        $name = '?' . $afterQuestionMark;
        do {
            $char = $input->current();

            if (! $firstChar && $char === $closing) {
                $name .= $char;
                break;
            }

            if ($firstChar && ctype_digit($char)) {
                throw Token\Exception\InvalidSubPattern::forInvalidCaptureGroupName();
            }

            if (! ctype_alnum($char)) {
                throw Token\Exception\InvalidSubPattern::forInvalidCaptureGroupName();
            }

            $name     .= $char;
            $firstChar = false;
        } while ($input->next() !== null);

        return [$token, SubPattern\Named::fromName($name)];
    }

    private function createEscapeSequence(Stream $input): Token
    {
        $current = $input->current();

        if (ctype_digit($current)) {
            $number = $current;
            while (ctype_digit($input->next())) {
                $number .= $input->current();
            }

            $input->moveTo($input->currentIndex() - 1);

            return SubPattern\Reference::create($number);
        }

        return Escaped\EscapedCharacter::fromCharacter($input->current());
    }
}
