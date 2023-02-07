# Explorer

[![Latest Version on Packagist][ico-version]][link-packagist]

Elasticsearch driver for Laravel Scout with the power of Elasticsearch's queries.

## Installation

Via Composer

```bash
composer require hilsonxhero/laravel-elastic-vision
```

You will need the configuration file to define your indexes:

```bash
php artisan vendor:publish --tag=explorer.config
```

Also do not forget to follow the [installation instructions for Laravel Scout](https://laravel.com/docs/scout#installation) and set the driver to `elastic`.

# Explorer documentation

- [Quickstart](quickstart.md)
- [Connection](connection.md)
- [Mapping properties in Elasticsearch](mapping.md)
- [Sorting search results](sorting.md)
- [Pagination and search result size](pagination.md)
- [Debugging](debugging.md)
- [Testing](testing.md)
- [Console commands](commands.md)
- [Text analysis](text-analysis.md)
- [Preparing data](preparing-data.md)
- [Advanced queries](advanced-queries.md)
- [Advanced index settings](index-settings.md)
- [Index aliases](index-aliases.md)
- [Aggregations](aggregations.md)

[ico-version]: https://img.shields.io/packagist/v/hilsonxhero/elasticvision.svg?style=flat-square
[ico-actions]: https://img.shields.io/github/workflow/status/hilsonxhero/laravel-elastic-vision/CI?label=CI%2FCD&style=flat-square
[link-packagist]: https://packagist.org/packages/hilsonxhero/elasticvision
