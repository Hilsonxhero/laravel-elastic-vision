<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\Analysis\Analyzer;

interface AnalyzerInterface
{
    public function getName(): string;

    public function setFilters(): void;

    public function getFilters(): array;

    public function build(): array;
}
