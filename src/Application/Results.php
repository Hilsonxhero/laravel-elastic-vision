<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Application;

use Countable;

class Results implements Countable
{
    private \Elastic\Elasticsearch\Response\Elasticsearch $rawResults;

    public function __construct(\Elastic\Elasticsearch\Response\Elasticsearch $rawResults)
    {
        $this->rawResults = $rawResults;
    }

    public function hits(): array
    {
        return $this->rawResults['hits']['hits'];
    }

    /** @return AggregationResult[] */
    public function aggregations(): array
    {
        if (!isset($this->rawResults['aggregations'])) {
            return [];
        }

        $aggregations = [];

        foreach ($this->rawResults['aggregations'] as $name => $rawAggregation) {
            $aggregations[] = new AggregationResult($name, $rawAggregation['buckets']);
        }

        return $aggregations;
    }

    public function count(): int
    {
        return $this->rawResults['hits']['total']['value'];
    }
}
