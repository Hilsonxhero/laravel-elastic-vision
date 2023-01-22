<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Application;

interface Explored
{
    public function getScoutKey();

    public function searchableAs();

    public function toSearchableArray();

    public function mappableAs(): array;
}
