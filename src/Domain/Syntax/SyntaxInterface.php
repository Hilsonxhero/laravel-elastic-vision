<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\Syntax;

interface SyntaxInterface
{
    public function build(): array;
}
