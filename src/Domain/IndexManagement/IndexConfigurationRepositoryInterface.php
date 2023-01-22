<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\IndexManagement;

interface IndexConfigurationRepositoryInterface
{
    /**
     * @return iterable<IndexConfigurationInterface>
     */
    public function getConfigurations(): iterable;

    /**
     * @throws IndexConfigurationNotFoundException
     */
    public function findForIndex(string $index): IndexConfigurationInterface;
}
