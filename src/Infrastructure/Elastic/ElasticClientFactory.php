<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
// use GuzzleHttp\Ring\Client\MockHandler;
use GuzzleHttp\Handler\MockHandler;

final class ElasticClientFactory
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function client(): Client
    {
        return $this->client;
    }

    public static function fake(FakeResponse $response): ElasticClientFactory
    {
        $handler = new MockHandler($response->toArray());
        $builder = ClientBuilder::create();
        $builder->setHosts(['localhost:9200']);
        $builder->setHandler($handler);
        return new self($builder->build());
    }
}
