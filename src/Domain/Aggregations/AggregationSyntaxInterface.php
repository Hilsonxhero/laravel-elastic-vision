<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\Aggregations;

interface AggregationSyntaxInterface
{
    public function build(): array;
}
