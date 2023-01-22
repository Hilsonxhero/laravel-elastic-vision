<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Application;

interface BePrepared
{
    public function prepare(array $searchable): array;
}
