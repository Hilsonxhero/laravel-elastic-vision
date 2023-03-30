<?php

namespace Hilsonxhero\ElasticVision\Providers;

use Laravel\Scout\Builder;
use Laravel\Scout\EngineManager;
use Elastic\Elasticsearch\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Hilsonxhero\ElasticVision\Infrastructure\Scout\ElasticEngine;
use Hilsonxhero\ElasticVision\Domain\Query\QueryProperties\QueryProperty;
use Hilsonxhero\ElasticVision\Infrastructure\Elastic\ElasticIndexAdapter;
use Hilsonxhero\ElasticVision\Infrastructure\Elastic\ElasticClientBuilder;
use Hilsonxhero\ElasticVision\Infrastructure\Elastic\ElasticClientFactory;
use Hilsonxhero\ElasticVision\Infrastructure\Elastic\ElasticDocumentAdapter;
use Hilsonxhero\ElasticVision\Domain\Aggregations\AggregationSyntaxInterface;
use Hilsonxhero\ElasticVision\Infrastructure\IndexManagement\ElasticIndexConfigurationRepository;


/**
 * @property array $must
 * @property array $must_not
 * @property array $should
 * @property array $filter
 * @property array $fields
 * @property array $compound
 * @property array $aggregations
 * @property array $queryProperties
 */
#[\AllowDynamicProperties]

class ServiceProvider extends BaseServiceProvider
{

    public array $bindings = [
        IndexAdapterInterface::class => ElasticIndexAdapter::class,
        DocumentAdapterInterface::class => ElasticDocumentAdapter::class,
        IndexConfigurationRepositoryInterface::class => ElasticIndexConfigurationRepository::class,
    ];

    public function register(): void
    {
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {

        $this->app->when(ElasticClientFactory::class)
            ->needs(Client::class)
            ->give(static fn () => ElasticClientBuilder::fromConfig(config())->build());

        $this->app->when(ElasticDocumentAdapter::class)
            ->needs(Client::class)
            ->give(static fn (Application $app) => $app->make(ElasticClientFactory::class)->client());

        $this->app->when(ElasticIndexConfigurationRepository::class)
            ->needs('$indexConfigurations')
            ->give(config('elasticvision.indexes') ?? []);

        $this->app->when(ElasticIndexConfigurationRepository::class)
            ->needs('$pruneOldAliases')
            ->give(config('elasticvision.prune_old_aliases') ?? true);

        resolve(EngineManager::class)->extend('elastic', function (Application $app) {
            return new ElasticEngine(
                $app->make(IndexAdapterInterface::class),
                $app->make(DocumentAdapterInterface::class),
                $app->make(IndexConfigurationRepositoryInterface::class)
            );
        });

        Builder::macro('must', function ($must) {
            $this->must[] = $must;
            return $this;
        });

        Builder::macro('must_not', function ($must_not) {
            $this->must_not[] = $must_not;
            return $this;
        });

        Builder::macro('should', function ($should) {
            $this->should[] = $should;
            return $this;
        });

        Builder::macro('filter', function ($filter) {
            $this->filter[] = $filter;
            return $this;
        });

        Builder::macro('field', function (string $field) {
            $this->fields[] = $field;
            return $this;
        });

        Builder::macro('newCompound', function ($compound) {
            $this->compound = $compound;
            return $this;
        });

        Builder::macro('aggregation', function (string $name, AggregationSyntaxInterface $aggregation) {
            $this->aggregations[$name] = $aggregation;
            return $this;
        });

        Builder::macro('property', function (QueryProperty $queryProperty) {
            $this->queryProperties[] = $queryProperty;
            return $this;
        });


        $this->mergeConfigFrom(__DIR__ . '/../config/elasticvision.php', 'elasticvision');

        $this->publishes([
            __DIR__ . '/../config/elasticvision.php' => config_path('elasticvision.php')
        ], 'elasticvision-config');
    }
}
