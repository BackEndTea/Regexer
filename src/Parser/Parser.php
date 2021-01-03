<?php

declare(strict_types=1);

namespace BackEndTea\Regexer\Parser;

use BackEndTea\Regexer\Node;

interface Parser
{
    public function parse(string $regex): Node;
}
