<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Domain\Query;

use Hilsonxhero\ElasticVision\Domain\Syntax\Sort;
use Hilsonxhero\ElasticVision\Domain\Query\Rescoring;
use Hilsonxhero\ElasticVision\Domain\Syntax\SyntaxInterface;
use Hilsonxhero\ElasticVision\Domain\Query\QueryProperties\QueryProperty;
use Hilsonxhero\ElasticVision\Domain\Aggregations\AggregationSyntaxInterface;

class Query implements SyntaxInterface
{
    private ?int $offset = null;

    private ?int $limit = null;

    /** @var Rescoring[]  */
    private array $rescoring = [];

    private array $fields = [];

    /** @var Sort[] */
    private array $sort = [];

    /** @var QueryProperty[]  */
    private array $queryProperties = [];

    private SyntaxInterface $query;

    /** @var AggregationSyntaxInterface[] */
    private array $aggregations = [];

    public static function with(SyntaxInterface $syntax): Query
    {
        $query = new self();
        $query->query = $syntax;
        return $query;
    }

    public function build(): array
    {
        $query = [
            'query' => $this->query->build()
        ];

        if ($this->hasPagination()) {
            $query['from'] = $this->offset;
        }

        if ($this->hasSize()) {
            $query['size'] = $this->limit;
        }

        if ($this->hasSort()) {
            $query['sort'] = $this->buildSort();
        }

        if ($this->hasFields()) {
            $query['fields'] = $this->fields;
        }

        if ($this->hasRescoring()) {
            $query['rescore'] = $this->buildRescoring();
        }

        if ($this->hasAggregations()) {
            $query['aggs'] = array_map(
                fn (AggregationSyntaxInterface $value) => $value->build(),
                $this->aggregations
            );
        }

        $allQueryProperties = array_map(
            static fn (QueryProperty $queryProperties) => $queryProperties->build(),
            $this->queryProperties
        );

        return array_merge($query, ...$allQueryProperties);
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    public function addQueryProperties(QueryProperty ...$properties): void
    {
        array_push($this->queryProperties, ...$properties);
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function setSort(array $sort): void
    {
        $this->sort = $sort;
    }

    public function setQuery(SyntaxInterface $query): void
    {
        $this->query = $query;
    }

    public function addRescoring(Rescoring $rescoring): void
    {
        $this->rescoring[] = $rescoring;
    }

    public function addAggregation(string $name, AggregationSyntaxInterface $aggregationItem): void
    {
        $this->aggregations[$name] = $aggregationItem;
    }

    public function hasAggregations(): bool
    {
        return !empty($this->aggregations);
    }

    private function hasPagination(): bool
    {
        return !is_null($this->offset);
    }

    private function hasSize(): bool
    {
        return !is_null($this->limit);
    }

    private function hasSort(): bool
    {
        return !empty($this->sort);
    }

    private function hasFields(): bool
    {
        return !empty($this->fields);
    }

    private function buildSort(): array
    {
        return array_map(static fn ($item) => $item->build(), $this->sort);
    }

    private function hasRescoring(): bool
    {
        return !empty($this->rescoring);
    }

    private function buildRescoring(): array
    {
        return array_map(fn (Rescoring $rescore) => $rescore->build(), $this->rescoring);
    }
}
