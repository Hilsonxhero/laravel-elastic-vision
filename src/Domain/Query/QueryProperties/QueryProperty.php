<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\Query\QueryProperties;

interface QueryProperty
{
    public function build(): array;
}
