<?php

declare(strict_types=1);

namespace Hilsonxhero\ElasticVision\Infrastructure\Elastic;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Config\Repository;

final class ElasticClientBuilder
{
    private const HOST_KEYS = ['host', 'port', 'scheme'];

    public static function fromConfig(Repository $config): ClientBuilder
    {
        $builder = ClientBuilder::create();

        $hostConnectionProperties = array_filter(
            $config->get('elasticvision.connection'),
            static fn ($key) => in_array($key, self::HOST_KEYS, true),
            ARRAY_FILTER_USE_KEY
        );

        // dd($config->get('elasticvision.connection'));

        // $builder->setHosts([$hostConnectionProperties]);
        // $builder->setHosts(["localhost:9200"]);
        $builder->setHosts([$config->get('elasticvision.connection.host')]);


        if ($config->has('elasticvision.additionalConnections')) {
            $builder->setHosts([$config->get('elasticvision.connection'), ...$config->get('elasticvision.additionalConnections')]);
        }

        // if ($config->has('elasticvision.connection.selector')) {
        //     $builder->setSelector($config->get('elasticvision.connection.selector'));
        // }

        if ($config->has('elasticvision.connection.api')) {
            $builder->setApiKey(
                $config->get('elasticvision.connection.api.id'),
                $config->get('elasticvision.connection.api.key')
            );
        }

        if ($config->has('elasticvision.connection.elasticCloudId')) {
            $builder->setElasticCloudId(
                $config->get('elasticvision.connection.elasticCloudId'),
            );
        }

        if ($config->has('elasticvision.connection.auth')) {
            $builder->setBasicAuthentication(
                $config->get('elasticvision.connection.auth.username'),
                $config->get('elasticvision.connection.auth.password')
            );
        }

        if ($config->has('elasticvision.connection.ssl.verify')) {
            $builder->setSSLVerification($config->get('elasticvision.connection.ssl.verify'));
        }

        if ($config->has('elasticvision.connection.ssl.key')) {
            [$path, $password] = self::getPathAndPassword($config->get('elasticvision.connection.ssl.key'));
            $builder->setSSLKey($path, $password);
        }

        if ($config->has('elasticvision.connection.ssl.cert')) {
            [$path, $password] = self::getPathAndPassword($config->get('elasticvision.connection.ssl.cert'));
            $builder->setSSLCert($path, $password);
        }
        // dd($builder);
        return $builder;
    }

    /**
     * @param array|string $config
     */
    private static function getPathAndPassword(mixed $config): array
    {
        return is_array($config) ? $config : [$config, null];
    }
}
