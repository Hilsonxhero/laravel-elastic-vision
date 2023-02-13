# Quickstart

Elasticsearch is "a distributed, open source search and analytics engine for all types of data" using REST and JSON.
The key concepts being are the index (a collection of documents), full-text searches and data ingestion (the processing of raw data).
Kibana is the interface to visualise and navigate through your elasticsearch indexes.

A very good way to get started with Elasticsearch is by creating a docker container and connecting it to your Laravel application.
You could use Tighten's Takeout for the Elasticsearch container, however it comes without Kibana.
To get started with Elasticsearch and Kibana, create a `docker-compose.yml` file and paste this configuration from [Elastic's documentation](https://www.elastic.co/guide/en/elastic-stack-get-started/current/get-started-docker.html).
After that, run `docker-compose up -d` and you will have an elasticsearch instance running at localhost:9200 and a Kibana instance at localhost:5601

```yaml
# For more information: https://laravel.com/docs/sail
version: '3'
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.1
            dockerfile: Dockerfile
            args:
                WWWGROUP: '${WWWGROUP}'
        image: sail-8.1/app
        extra_hosts:
            - 'host.docker.internal:host-gateway'
        ports:
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'

        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
            XDEBUG_CONFIG: '${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
        depends_on:
            - mysql
    mysql:
        image: 'mysql/mysql-server:8.0'
        ports:
            - '${FORWARD_DB_PORT:-3306}:3306'
        environment:
            MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
            MYSQL_DATABASE: '${DB_DATABASE}'
            MYSQL_USER: '${DB_USERNAME}'
            MYSQL_PASSWORD: '${DB_PASSWORD}'
        volumes:
            - 'sail-mysql:/var/lib/mysql'
            - './vendor/laravel/sail/database/mysql/create-testing-database.sh:/docker-entrypoint-initdb.d/10-create-testing-database.sh'
        networks:
            - sail
        healthcheck:
            test: [ "CMD", "mysqladmin", "ping", "-p${DB_PASSWORD}" ]
            retries: 3
            timeout: 5s

    phpmyadmin:
        image: phpmyadmin/phpmyadmin
        ports:
            - 8090:80
        environment:
            PMA_HOST: mysql
            PMA_USER: ${DB_USERNAME}
            PMA_PASSWORD: ${DB_PASSWORD}
            PWA_PORT: 3306

        volumes:
            - /sessions

        depends_on:
            - mysql
        networks:
            - sail
        links:
            - mysql
    elasticsearch:
        image: docker.elastic.co/elasticsearch/elasticsearch:8.6.0
        container_name: elasticsearch
        environment:
            - xpack.security.enabled=false
            - "discovery.type=single-node"
        networks:
            - sail
           volumes:
             - data01:/usr/share/elasticsearch/data
        ports:
            - "9200:9200"
        deploy:
            resources:
                limits:
                    memory: "2000M"
    kibana:
        container_name: kibana
        image: docker.elastic.co/kibana/kibana:8.6.0
        environment:
            - ELASTICSEARCH_HOSTS=http://elasticsearch:9200
        ports:
            - "5601:5601"
        networks:
            - sail
        depends_on:
            - elasticsearch
networks:
    sail:
        driver: bridge
volumes:
    sail-mysql:
        driver: local

    sail-phpmyadmin:
        driver: local


```

elasticvision.php config

```php

<?php

declare(strict_types=1);

return [
    /*
     * There are different options for the connection. Since ElasticVision uses the Elasticsearch PHP SDK
     * under the hood, all the host configuration options of the SDK are applicable here. See
     * https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html
     */
    'connection' => [
        'host' => 'elasticsearch:9200',
        'port' => '9200',
        'scheme' => 'http',
    ],

    /**
     * An index may be defined on an Eloquent model or inline below. A more in depth explanation
     * of the mapping possibilities can be found in the documentation of ElasticVision's repository.
     */
    'indexes' => [
        \Modules\Product\Entities\Product::class
    ],

    /**
     * You may opt to keep the old indices after the alias is pointed to a new index.
     * A model is only using index aliases if it implements the Aliased interface.
     */
    'prune_old_aliases' => true,
];


```

The thing you should do next, if you haven't already, is follow the [Laravel Scout](https://laravel.com/docs/scout) installation and configuration.
The only point where you should diverge from the docs is the driver for scout (in config/scout.php):

```php
'driver' => 'elastic',
```

After that, you can define your first index in config/elasticvision.php:

```php
'indexes' => [
    'posts_index' => [
        'properties' => [
            'id' => 'keyword',
            'title' => 'text',
        ],
    ]
]
```

Upon saving the file, run `php artisan elastic:create` to create this index, and `php artisan scout:import "App\Models\Post"` to add your posts as documents to the index.
This of course assumes that you have a Post model (with an ID and title attribute) and a couple of entries of them in your database.
As mentioned before, Laravel Scout also has a few requirements explained in its documentation for your models.

To query your posts, use Scout's search method to find stuff, for example:

```php
$ipsum = App\Models\Post::search('Lorem')->get();
```

Enjoy!
