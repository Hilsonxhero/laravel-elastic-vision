<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Application;

use Hilsonxhero\ElasticVision\Domain\IndexManagement\IndexConfigurationInterface;

interface IndexAdapterInterface
{
    public function create(IndexConfigurationInterface $indexConfiguration): void;

    public function pointToAlias(IndexConfigurationInterface $indexConfiguration): void;

    public function delete(IndexConfigurationInterface $indexConfiguration): void;

    public function flush(string $index): void;

    public function createNewWriteIndex(IndexConfigurationInterface $indexConfiguration): string;

    public function ensureIndex(IndexConfigurationInterface $indexConfiguration): void;
}
