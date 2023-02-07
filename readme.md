# Elastic Vision

[![Latest Version on Packagist][ico-version]][link-packagist]

Elasticsearch driver for Laravel Scout with the power of Elasticsearch's queries.

## Installation

Via Composer

```bash
composer require hilsonxhero/laravel-elastic-vision
```

You will need the configuration file to define your indexes:

```bash
php artisan vendor:publish --tag=elasticvision-config
```

Also do not forget to follow the [installation instructions for Laravel Scout](https://laravel.com/docs/scout#installation),
and in your Laravel Scout config, set the driver to `elastic`.

## Usage

Be sure to also have a look at the [docs](docs/index.md) to see what is possible!

### Configuration

You may either define the mapping for you index in the config file:

```php
return [
    'indexes' => [
        'posts_index' => [
            'properties' => [
                'id' => 'keyword',
                'title' => 'text',
            ],
        ]
    ]
];
```

Or you may define the model for the index, and the rest will be decided for you:

```php
return [
    'indexes' => [
        \App\Models\Article::class
    ],
];
```

In the last case you may implement the `Explored` interface and overwrite the mapping with the `mappableAs()` function.

Essentially this means that it is up to you whether you like having it all together in the model, or separately in the config file.

# documentation

- [Quickstart](./docs/quickstart.md)
- [Connection](./docs/connection.md)
- [Mapping properties in Elasticsearch](./docs/mapping.md)
- [Sorting search results](./docs/sorting.md)
- [Pagination and search result size](./docs/pagination.md)
- [Debugging](./docs/debugging.md)
- [Testing](./docs/testing.md)
- [Console commands](./docs/commands.md)
- [Text analysis](./docs/text-analysis.md)
- [Preparing data](./docs/preparing-data.md)
- [Advanced queries](./docs/advanced-queries.md)
- [Advanced index settings](./docs/index-settings.md)
- [Index aliases](./docs/index-aliases.md)
- [Aggregations](./docs/aggregations.md)

### Advanced queries

The documentation of Laravel Scout states that "more advanced "where" clauses are not currently supported".
Only a simple check for ID is possible besides the standard fuzzy term search:

```php
$posts = Post::search('lorem ipsum')->get();
```

Explorer expands your possibilities using query builders to write more complex queries.

For example, to get all posts that:

- are published
- have "lorem" somewhere in the document
- have "ipsum" in the title
- maybe have a tag "featured", if so boost its score by 2

You could execute this search query:

```php
$articles = Article::search('lorem')
    ->must(new Matching('title', 'ipsum'))
    ->should(new Terms('tags', ['featured'], 2))
    ->filter(new Term('published', true))
    ->get();
```

### Commands

Be sure you have configured your indexes first in `config/elasticvision.php` and run the Scout commands.

## Credits

- [Hilsonxhero][link-author]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hilsonxhero/elasticvision.svg?style=flat-square
[ico-actions]: https://img.shields.io/github/workflow/status/hilsonxhero/laravel-elastic-vision/CI?label=CI%2FCD&style=flat-square
[link-packagist]: https://packagist.org/packages/hilsonxhero/laravel-elastic-vision
[link-author]: https://github.com/Hilsonxhero
[link-contributors]: ../../contributors
