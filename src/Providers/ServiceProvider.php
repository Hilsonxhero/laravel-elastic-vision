<?php

namespace Hilsonxhero\ElasticVision\Providers;

use Laravel\Scout\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Hilsonxhero\ElasticVision\Infrastructure\Scout\ElasticEngine;
use Hilsonxhero\ElasticVision\Infrastructure\Elastic\ElasticDocumentAdapter;
use Hilsonxhero\ElasticVision\Infrastructure\Elastic\ElasticIndexAdapter;
use Hilsonxhero\ElasticVision\Infrastructure\Elastic\ElasticClientBuilder;
use Hilsonxhero\ElasticVision\Infrastructure\Elastic\ElasticClientFactory;
use Hilsonxhero\ElasticVision\Domain\Aggregations\AggregationSyntaxInterface;
use Hilsonxhero\ElasticVision\Infrastructure\IndexManagement\ElasticIndexConfigurationRepository;
use Laravel\Scout\EngineManager;

class ServiceProvider extends BaseServiceProvider
{

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
        $this->app->bind(ElasticClientFactory::class, function () {
            return new ElasticClientFactory(ElasticClientBuilder::fromConfig(config())->build());
        });

        $this->app->bind(IndexAdapterInterface::class, ElasticIndexAdapter::class);
        $this->app->bind(DocumentAdapterInterface::class, ElasticDocumentAdapter::class);
        $this->app->bind(IndexChangedCheckerInterface::class, ElasticIndexChangedChecker::class);

        $this->app->bind(IndexConfigurationRepositoryInterface::class, function () {
            return new ElasticIndexConfigurationRepository(
                config('elasticvision.indexes') ?? [],
                config('elasticvision.prune_old_aliases'),
            );
        });

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


        $this->mergeConfigFrom(__DIR__ . '/../config/elasticvision.php', 'elasticvision');

        $this->publishes([
            __DIR__ . '/../config/elasticvision.php' => config_path('elasticvision.php')
        ], 'elasticvision-config');
    }
}
