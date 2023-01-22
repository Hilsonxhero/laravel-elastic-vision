<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\IndexManagement;

interface IndexAliasConfigurationInterface
{
    public function shouldOldAliasesBePruned(): bool;

    public function getIndexName(): string;

    public function getAliasName(): string;

    public function getHistoryAliasName(): string;

    public function getWriteAliasName(): string;
}
