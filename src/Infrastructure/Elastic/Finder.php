<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Hilsonxhero\ElasticVision\Application\Results;
use Hilsonxhero\ElasticVision\Application\SearchCommandInterface;

class Finder
{
    public function __construct(
        private Client $client,
        private SearchCommandInterface $builder,
    ) {
    }

    public function find(): Results
    {
        $query = [
            'index' => $this->builder->getIndex(),
            'body' => $this->builder->buildQuery(),
        ];

        $rawResults = $this->client->search($query);

        // dd($rawResults);

        return new Results($rawResults);
    }
}
