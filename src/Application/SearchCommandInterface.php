<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Application;

interface SearchCommandInterface
{
    public function getIndex(): ?string;

    public function buildQuery(): array;
}
