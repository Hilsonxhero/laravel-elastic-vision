<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Application;

use Hilsonxhero\ElasticVision\Application\Operations\Bulk\BulkOperationInterface;

interface DocumentAdapterInterface
{
    /** Allows to perform multiple index/update/delete operations in a single request. */
    public function bulk(BulkOperationInterface $command);

    public function update(string $index, string $id, array $data);

    public function delete(string $index, string $id): void;

    public function search(SearchCommandInterface $command): Results;
}
