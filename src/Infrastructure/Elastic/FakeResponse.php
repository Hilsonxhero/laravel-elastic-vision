<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use GuzzleHttp\Ring\Future\FutureArrayInterface;
use Hilsonxhero\ElasticVision\Application\Results;
use Hilsonxhero\ElasticVision\Application\SearchCommandInterface;
use Webmozart\Assert\Assert;

class FakeResponse
{
    private int $statusCode;

    private $body;

    /**
     * @param resource $body
     */
    public function __construct(int $statusCode, $body)
   {
       Assert::resource($body);
       $this->statusCode = $statusCode;
       $this->body = $body;
   }

    public function toArray(): array
    {
        return [
            'status' => $this->statusCode,
            'transfer_stats' => ['total_time' => 100],
            'body' => $this->body,
            'effective_url' => 'localhost'
        ];
    }
}
