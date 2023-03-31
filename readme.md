# 🔭 Elastic Vision

[![Latest Version on Packagist][ico-version]][link-packagist]

Elasticsearch driver for Laravel Scout with the power of Elasticsearch's queries.

## Installation

Via Composer

```bash
composer require hilsonxhero/elasticvision
```

You will need the configuration file to define your indexes:

```bash
php artisan vendor:publish --tag=elasticvision-config
```

Also do not forget to follow the [installation instructions for Laravel Scout](https://laravel.com/docs/scout#installation),
and in your Laravel Scout config, set the driver to `elastic`.

### Configuration

You may either define the mapping for you index in the elasticvision config file:

```php
return [
    'indexes' => [
        \App\Models\Product::class,
    ],
];
```

```bash
php artisan scout:index products
```

```bash
php artisan scout:import "\App\Models\Product"
```

## Usage

In the last case you may implement the `Explored` interface and overwrite the mapping with the `mappableAs()` function.

# 📕 documentation

Essentially this means that it is up to you whether you like having it all together in the model, or separately in the config file.

# Term query

Returns documents that contain an exact term in a provided field.

You can use the term query to find documents based on a precise value such as a price, a product ID, or a username.

```php
use App\Models\Post;

$posts = Post::search('lorem')
    ->filter(new Term('published', true))
    ->get();
```

# Terms query

Returns documents that contain one or more exact terms in a provided field.

The terms query is the same as the term query, except you can search for multiple values. A document will match if it contains at least one of the terms. To search for documents that contain more than one matching term.

```php
use App\Models\Post;

$posts = Post::search('lorem')
    ->should(new Terms('tags', ['featured'], 2))
    ->get();
```

### Top-level parameters for terms

`field`

(Optional, object) Field you wish to search.

`boost`

(Optional, float) Floating point number used to decrease or increase the relevance scores of a query. Defaults to 1.0.

# Sorting

By default, your search results will be sorted by their score according to Elasticsearch.
If you want to step in and influence the sorting you may do so using the default `orderBy()` function from Laravel Scout.

```php
use App\Models\Post;

$results = Post::search('Self-steering')
    ->orderBy('published_at', 'desc')
    ->get();
```

# Range

Returns documents that contain terms within a provided range.

```php
use App\Models\User;

$results = User::search('fugiat')->must(new Range('age',['gte' => 18, 'lte' => 35]))->get();
```

# Regexp query

Returns documents that contain terms matching a [regular expression](https://en.wikipedia.org/wiki/Regular_expression).

A regular expression is a way to match patterns in data using placeholder characters, called operators. For a list of operators supported by the regexp query, see [Regular expression syntax](https://www.elastic.co/guide/en/elasticsearch/reference/current/regexp-syntax.html).

```php
use App\Models\User;

$results = User::search('fugiat')->must(new RegExp('username', 'k.*y','ALL',false))->get();
```

### Top-level parameters for regexp

`field`

(Optional, object) Field you wish to search.

`value`

(Required, string) Regular expression for terms you wish to find in the provided `<field>`. For a list of supported operators, see [Regular expression syntax](https://www.elastic.co/guide/en/elasticsearch/reference/current/regexp-syntax.html).

`flags`

(Optional, string) Enables optional operators for the regular expression. For valid values and more information, see [Regular expression syntax](https://www.elastic.co/guide/en/elasticsearch/reference/current/regexp-syntax.html#regexp-optional-operators).

`case_insensitive`

(Optional, Boolean) Allows case insensitive matching of the regular expression value with the indexed field values when set to true. Default is false which means the case sensitivity of matching depends on the underlying field’s mapping.

`boost`

(Optional, float) Floating point number used to decrease or increase the relevance scores of a query. Defaults to 1.0.

# Wildcard query

Returns documents that contain terms matching a wildcard pattern.

A wildcard operator is a placeholder that matches one or more characters. For example, the \* wildcard operator matches zero or more characters. You can combine wildcard operators with other characters to create a wildcard pattern.

```php
use App\Models\User;

$users = User::search()
    ->should(new Wildcard('username','ki*y'))
    ->get();
```

# Match query

Returns documents that match a provided text, number, date or boolean value. The provided text is analyzed before matching.

The match query is the standard query for performing a full-text search, including options for fuzzy matching.

```php
use App\Models\Article;

$articles = Article::search()
    ->must(new Matching('title','ipsum'))
    ->get();
```

# Match phrase prefix query

Returns documents that contain the words of a provided text, in the same order as provided. The last term of the provided text is treated as a prefix, matching any words that begin with that term.

```php
use App\Models\User;

$users = User::search()
    ->should(new MatchPhrasePrefix('message','quick brown f'))
    ->get();
```

# Match phrase query

The `match_phrase` query analyzes the text and creates a `phrase` query out of the analyzed text. For example:

```php
use App\Models\User;

$users = User::search()
    ->should(new MatchPhrase('message','this is a test'))
    ->get();
```

# Nested query

Wraps another query to search nested fields.

The `nested` query searches nested field objects as if they were indexed as separate documents. If an object matches the search, the `nested` query returns the root parent document.

```php
use App\Models\Product;

$products = Product::search()
    ->must(new Nested('category', new Term('category.id', 2)))
    ->get();
```

```php
use App\Models\Product;

$search = Product::search("lorem");

// $feature_ids = array([4 => [1,2], 5 => [1,2]])

foreach (request()->feature_id as $key => $value) {
  $query = new BoolQuery();
  $query->must(new Term('features.feature_id', $key));
  $query->must(new Terms('features.feature_value_id', $value));
  $boolQuery->add('must', new Nested('features', $query));
}

 $search->newCompound($boolQuery);

 $products = $search->paginate(15);
```

A phrase query matches terms up to a configurable slop (which defaults to 0) in any order. Transposed terms have a slop of 2.

The analyzer can be set to control which analyzer will perform the analysis process on the text. It defaults to the field explicit mapping definition, or the default search analyzer, for example:

# Index settings

Most of the configuration you will be doing through the [mapping](mapping.md) of your index.
However, if for example you want to define more advanced Elasticsearch settings such as [analyzers](https://www.elastic.co/guide/en/elasticsearch/reference/current/analyzer.html) or [tokenizers](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-tokenizers.html) you need to do so using index settings.

Be aware that any time you change the index settings, you need to [recreate](commands.md) the index.

To start using index settings, we will expand on the Post model with an `indexSettings` function to set an analyzer.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Hilsonxhero\ElasticVision\Application\Explored;
use Hilsonxhero\ElasticVision\Application\IndexSettings;
use Laravel\Scout\Searchable;

class Post extends Model implements Explored, IndexSettings
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['title', 'published'];

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'title' => 'text',
            'published' => 'boolean',
            'created_at' => 'date',
        ];
    }

    public function indexSettings(): array
    {
        return [
            'analysis' => [
                'analyzer' => [
                    'standard_lowercase' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['lowercase'],
                    ],
                ],
            ],
        ];
    }
}
```

# Text analysis

Text analysis is set as part of the [index settings](index-settings.md).

The following example creates a synonym analyzer, the end result would be that when you search for 'Vue' you (also) get the results for 'React'.
To make sure the synonyms match all cases, the `lowercase` filter is run as well.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Hilsonxhero\ElasticVision\Application\Explored;
use Hilsonxhero\ElasticVision\Application\IndexSettings;
use Hilsonxhero\ElasticVision\Domain\Analysis\Analysis;
use Hilsonxhero\ElasticVision\Domain\Analysis\Analyzer\StandardAnalyzer;
use Hilsonxhero\ElasticVision\Domain\Analysis\Filter\SynonymFilter;
use Laravel\Scout\Searchable;

class Post extends Model implements Explored, IndexSettings
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['title', 'published'];

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'title' => [
                'type' => 'text',
                'analyzer' => 'frameworks',
            ],
            'published' => 'boolean',
            'created_at' => 'date',
        ];
    }

    public function indexSettings(): array
    {
        $synonymFilter = new SynonymFilter();
        $synonymFilter->setSynonyms(['vue => react']);

        $synonymAnalyzer = new StandardAnalyzer('frameworks');
        $synonymAnalyzer->setFilters(['lowercase', $synonymFilter]);

        return (new Analysis())
            ->addAnalyzer($synonymAnalyzer)
            ->addFilter($synonymFilter)
            ->build();
    }
}
```

# Aggregations

Aggregations are part of your search query and can summarise your data.
You can read more about aggregations in Elasticsearch in the [official documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations.html).
At this moment not all aggregation types are build in, but creating the missing ones should be doable (and these additions to the package are very welcome).

Adding aggregations makes your search query more advanced.
Here is an example from the demo application:

```php
$search = Cartographer::search();
$search->aggregation('places', new TermsAggregation('place'));

$results = $search->raw();
$aggregations = $results->aggregations();
```

This will return an array of metrics on how many times every place is present in the Elasticsearch index.

It is very easy to create synonym filters and analyzers, but be aware that they are 'expensive' to run for Elasticsearch.
Before turning to synonyms, see if you can use wildcards or fuzzy queries.

### ⚡ Advanced queries

The documentation of Laravel Scout states that "more advanced "where" clauses are not currently supported".
Only a simple check for ID is possible besides the standard fuzzy term search:

```php
$categories = Category::search('lorem ipsum')->filter(new MatchPhrase('status', 'enable'))->take(15)->get();
```

ElasticVision expands your possibilities using query builders to write more complex queries.

```php
class Product extends Model implements Explored
{
    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'title_fa' => [
                'type' => 'text',
                'analyzer' => 'my_analyzer',
            ],
            'title_en' => [
                'type' => 'text',
            ],
            'status' => [
                'type' => 'text',
            ],
            'category' => 'nested',
            'features' => 'nested',
            'variants' => 'nested',
            'has_stock' => 'boolean',
        ];
    }


    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'category' => $this->category,
            'features' => $this->features,
            'variants' => ProductVariantResource::collection($this->variants)->toArray(true),
            'has_stock' => $this->has_stock,
        ];
    }

    public function indexSettings(): array
    {
        return [
            "analysis" => [
                "analyzer" => [
                    "my_analyzer" => [
                        "type" => "custom",
                        "tokenizer" => "standard",
                        "filter" => ["lowercase", "my_filter"]
                    ]
                ],
                "filter" => [
                    "my_filter" => [
                        "type" => "ngram",
                        "min_gram" => 2,
                    ]
                ]
            ],
            "index" => [
                "max_ngram_diff" => 13
            ]
        ];
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'products';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
        public function features()
    {
        return $this->hasMany(ProductFeature::class);
    }

    /**
     * check inventory of product variations
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */

    protected function hasStock(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->variants()->sum('stock') > 0 ? true : false
        );
    }

}


```

For example, to get all posts that:

- have enable status
- have a category with id 2
- have "ipsum" in the title and description
- have any features

You could execute this search query:

```php
        $boolQuery = new BoolQuery();

        $search = Product::search("ipsum")
            ->field('title')
            ->field('description')
            ->filter(new MatchPhrase('status', 'enable'))
            ->must(new Nested('category', new MatchPhrase('category.id',2)));

        if (request()->filled('available_stock')) {
            $search->filter(new Term('has_stock', true));
        }

        // request feature_ids value

        // $feature_ids = array([4 => [1,2], 5 => [1,2]])

        if (request()->filled('feature_ids')) {
            foreach (request()->feature_ids as $key => $value) {
                $query = new BoolQuery();
                $query->must(new MatchPhrase('features.feature_id', $key));
                $query->must(new Terms('features.feature_value_id', $value));
                $boolQuery->add('must', new Nested('features', $query));
            }
        }

        if (request()->filled('max_price') && request()->filled('min_price')) {
            $boolQuery->add('must', new Nested('variants', new Range(
                'variants.selling_price',
                ['gte' => request()->min_price]
            )));
            $boolQuery->add('must', new Nested('variants', new Range(
                'variants.selling_price',
                ['lte' => request()->max_price]
            )));
            $boolQuery->add('must_not', new Nested('variants', new Range(
                'variants.selling_price',
                ['lt' => request()->min_price]
            )));
            $boolQuery->add('must_not', new Nested('variants', new Range(
                'variants.selling_price',
                ['gt' => request()->max_price]
            )));
        }

        $search->newCompound($boolQuery);

        $products = $search->paginate(15);

        return $products;
```

# Debugging

Sometimes you might wonder why certain results are or aren't returned.

Here is an example from the ElasticVision demo app, although not with a complex query:

```php
class SearchController
{
    public function __invoke(SearchFormRequest $request)
    {
        $people = Cartographer::search($request->get('keywords'))->get();

        return view('search', [
            'people' => $people,
        ]);
    }
}
```

To debug this search query you can call the static `debug` method on the Elastic Engine for Laravel Scout:

```php
use Hilsonxhero\ElasticVision\Infrastructure\Scout\ElasticEngine;

$debug = ElasticEngine::debug();
```

The debug class that this method returns can give you the last executed query as an array or as json.
You should be able to copy-paste the json as a direct query to Elasticsearch.

```php
$lastQueryAsArray = ElasticEngine::debug()->array();
$lastQueryAsJson = ElasticEngine::debug()->json();
```

# Console commands

## Create

Use Laravel Scout import command to create and update indices.

```
php artisan scout:import <model>
```

For example, if your model is "App\Models\Post" then command would be like this:

```
php artisan scout:import "App\Models\Post"
```

If you want to recreate an index, first make sure it's deleted and then create it.
Follow up with a scout import to refill the index as well.

## Update Aliased Indexes

If you are using Aliased Indexes, you should use this command instead of `scout:import`

```
php artisan elastic:update <index?>
```

You can specify an index or choose to omit it and the command will update all your indexes.
For example, if your model is "App\Model\Post" and the index is "posts":

```
php artisan elastic:update posts
```

## Delete

```
php artisan scout:delete-index <model>
```

Use Laravel Scount delete-index command to delete the indices.

### Commands

Be sure you have configured your indexes first in `config/elasticvision.php` and run the Scout commands.

# 📕 documentation

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

[ico-version]: https://img.shields.io/packagist/v/hilsonxhero/elasticvision.svg?style=flat-square
[ico-actions]: https://img.shields.io/github/workflow/status/hilsonxhero/laravel-elastic-vision/CI?label=CI%2FCD&style=flat-square
[link-packagist]: https://packagist.org/packages/hilsonxhero/laravel-elastic-vision
[link-author]: https://github.com/Hilsonxhero
[link-contributors]: ../../contributors
