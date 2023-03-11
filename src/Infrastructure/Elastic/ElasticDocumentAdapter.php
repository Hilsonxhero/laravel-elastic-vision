<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Hilsonxhero\ElasticVision\Application\DocumentAdapterInterface;
use Hilsonxhero\ElasticVision\Application\Operations\Bulk\BulkOperationInterface;
use Hilsonxhero\ElasticVision\Application\Results;
use Hilsonxhero\ElasticVision\Application\SearchCommandInterface;
use Elastic\Elasticsearch\Exception\MissingParameterException;

final class ElasticDocumentAdapter implements DocumentAdapterInterface
{
    private Client $client;

    public function __construct(ElasticClientFactory $clientFactory)
    {
        $this->client = $clientFactory->client();
    }

    public function bulk(BulkOperationInterface $command)
    {
        return $this->client->bulk([
            'body' => $command->build(),
        ]);
    }

    public function update(string $index, $id, array $data)
    {
        return $this->client->index([
            'index' => $index,
            'id' => $id,
            'body' => $data,
        ]);
    }

    public function delete(string $index, $id): void
    {
        try {
            $this->client->delete([
                'index' => $index,
                'id' => $id
            ]);
        } catch (MissingParameterException) {
        }
    }

    public function search(SearchCommandInterface $command): Results
    {
        return (new Finder($this->client, $command))->find();
    }
}
