<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\Analysis\Filter;

interface FilterInterface
{
    public function getName(): string;

    public function build(): array;
}
