<?php

namespace Hilsonxhero\ElasticVision\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Hilsonxhero\ElasticVision\Domain\Query\Query;
use Hilsonxhero\ElasticVision\Domain\Syntax\Sort;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Hilsonxhero\ElasticVision\Domain\Query\Rescoring;
use Hilsonxhero\ElasticVision\Domain\Syntax\MatchAll;
use Hilsonxhero\ElasticVision\Domain\Aggregations\TermsAggregation;

class QueryTest extends TestCase
{

    use RefreshDatabase;

    private MatchAll $syntax;

    private Query $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->syntax = new MatchAll();
        $this->query = Query::with($this->syntax);
    }

    /**
     * test it builds query object.
     *
     * @return void
     */

    public function test_it_builds_query(): void
    {
        $result = $this->query->build();
        self::assertEquals(['query' => $this->syntax->build()], $result);
        self::assertFalse($this->query->hasAggregations());
    }

    public function test_it_builds_query_with_sort(): void
    {
        $sort = new Sort('field', Sort::DESCENDING);
        $this->query->setSort([$sort]);

        $result = $this->query->build();
        $this->assertEquals([$sort->build()], $result['sort'] ?? null);
    }

    public function test_it_builds_query_with_pagination(): void
    {
        $this->query->setLimit(10);
        $this->query->setOffset(30);

        $result = $this->query->build();
        $this->assertEquals(30, $result['from'] ?? null);
        $this->assertEquals(10, $result['size'] ?? null);
    }

    public function test_it_needs_both_limit_and_offset_for_pagination(): void
    {
        $this->query->setLimit(10);

        $result = $this->query->build();
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayNotHasKey('from', $result);

        $this->query->setLimit(null);
        $this->query->setOffset(null);
        $result = $this->query->build();
        $this->assertArrayNotHasKey('size', $result);
        $this->assertArrayNotHasKey('from', $result);
    }

    public function test_it_builds_query_with_limit_alone_for_custom_total_size(): void
    {
        $this->query->setLimit(10);

        $result = $this->query->build();
        self::assertArrayNotHasKey('from', $result);
        self::assertEquals(10, $result['size']);
    }


    public function test_it_builds_query_with_fields(): void
    {
        $this->query->setFields(['field.one']);
        $result = $this->query->build();
        self::assertEquals(['field.one'], $result['fields'] ?? null);
    }

    public function test_it_builds_query_with_rescoring(): void
    {
        $rescoring = new Rescoring();
        $rescoring->setQuery(new MatchAll());
        $this->query->addRescoring($rescoring);
        $this->query->addRescoring($rescoring);

        $result = $this->query->build();

        self::assertEquals([
            'query' => ['match_all' => (object)[]],
            'rescore' => [
                $rescoring->build(),
                $rescoring->build()
            ]
        ], $result);
    }

    public function test_it_builds_query_with_aggregations(): void
    {
        $this->query->addAggregation(':name:', new TermsAggregation(':field:'));

        self::assertTrue($this->query->hasAggregations());

        self::assertEquals([
            'query' => ['match_all' => (object)[]],
            'aggs' => [
                ':name:' => [
                    'terms' => [
                        'field' => ':field:',
                        'size' => 10
                    ]
                ]
            ]
        ], $this->query->build());
    }
}
