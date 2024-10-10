# Laravel Scout helpers

This package allows to easily support and switch between few scout drivers in
your application.

The main goal is to manage complex custom searches.

This simple case will work on any scout driver.

```php
$users = User::search('query')
    ->where('created_at', '>', now()->subYear())
    ->orderBy('created_at');
```

But if we need more complex search, we should customize search via callback:

```php
$users = User::search('query', function () {
        // Customize search here
        // Function arguments differs depending on driver
    })
    ->orderBy('created_at');
```

Different scout drivers has different callback signature, so we can not 
switch between drivers without punishment. We need to implement custom 
callback logic for every scout driver we plan to support.

This package provides _invokable_ abstract class, that incorporates custom 
searches for every driver. 
Implement one or few of 
`\Codewiser\Scout\Concerns\ScoutsDatabase`,
`\Codewiser\Scout\Concerns\ScoutsMeilisearch`, 
`\Codewiser\Scout\Concerns\ScoutsAlgolia` —
depending on what you want your application to support.

```php
use Codewiser\Scout\Concerns\Scout;
use Codewiser\Scout\Concerns\ScoutsDatabase;
use Codewiser\Scout\Concerns\ScoutsMeilisearch;
use Codewiser\Scout\Meilisearch\MeilisearchBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder;

class UserScout extends Scout implements ScoutsDatabase, ScoutsMeilisearch
{
    public function __construct(public ?int $min_comments) {
        //
    }
    
    public function database(Builder $builder) : Builder
    {
        return $builder
            ->has('comments', '>=', $this->min_comments);
    }
    
    public function meilisearch(MeilisearchBuilder $builder) : MeilisearchBuilder
    {
        return $builder
            ->where('comments_count', '>=', $this->min_comments)
    }
}
```

    As you can see, we provide `MeilisearchBuilder` with familiar interface, that
    is helpful for building Meilisearch filters.

As this class is _invokable_ we may use it as a callback when searching.

```php
public function index(\Illuminate\Http\Request $request)
{
    $scouter = new UserScout(min_comments: $request->input('filter.min_comments'));
    
    $users = User::search('query', $scouter)
        ->orderBy('created_at');
    
    // Debug search filters
    dump($scouter->debug());
    
    return $users->paginate();
}
```

## Meilisearch config

Out-of-the-box, we should configure Meiliserach index settings
in `config/scout.php`:
https://laravel.com/docs/10.x/scout#configuring-filterable-data-for-meilisearch

```php
use App\Models\User;
use App\Models\Flight;
 
'meilisearch' => [
    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'key' => env('MEILISEARCH_KEY', null),
    'index-settings' => [
        User::class => [
            'filterableAttributes'=> ['id', 'name', 'email'],
            'sortableAttributes' => ['created_at'],
            // Other settings fields...
        ],
        Flight::class => [
            'filterableAttributes'=> ['id', 'destination'],
            'sortableAttributes' => ['updated_at'],
        ],
    ],
],
```

This package allows to configure Meilisearch using php attributes.

```php
use Codewiser\Scout\Attributes\MeilisearchFilterableAttributes;
use Codewiser\Scout\Attributes\MeilisearchSortableAttributes;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Searchable;
    
    #[MeilisearchFilterableAttributes(['id', 'name', 'email'])]
    #[MeilisearchSortableAttributes(['created_at'])]
    public function toSearchableArray()
    {
        //
    }
}
```

Then just enumerate searchable classes in `config/scout.php`:

```php
use App\Models\User;
 
'meilisearch' => [
    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'key' => env('MEILISEARCH_KEY', null),
    'searchable' => [
        User::class
    ],
],
```

### Console

Use `scout:meilisearch-rebuild` command to rebuild all registered Meilisearch 
indexes.  