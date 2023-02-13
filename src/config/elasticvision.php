<?php

declare(strict_types=1);

return [
    /*
     * There are different options for the connection. Since ElasticVision uses the Elasticsearch PHP SDK
     * under the hood, all the host configuration options of the SDK are applicable here. See
     * https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html
     */
    'connection' => [
        'host' => 'localhost:9200',
        'port' => '9200',
        'scheme' => 'http',
    ],

    /**
     * An index may be defined on an Eloquent model or inline below. A more in depth explanation
     * of the mapping possibilities can be found in the documentation of ElasticVision's repository.
     */
    'indexes' => [
        // \App\Models\Post::class
    ],

    /**
     * You may opt to keep the old indices after the alias is pointed to a new index.
     * A model is only using index aliases if it implements the Aliased interface.
     */
    'prune_old_aliases' => true,
];
