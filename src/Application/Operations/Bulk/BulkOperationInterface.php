<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Application\Operations\Bulk;

interface BulkOperationInterface
{
    public function build(): array;
}
