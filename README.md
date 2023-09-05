# Laravel Meilisearch

Laravel helper for Meilisearch Scout driver.

Out-of-the-box, we should configure Meiliserach index settings in `config/scout.php`: 
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

## Using `Attributes`:

```php
class User extends \Illuminate\Database\Eloquent\Model
{
    use \Laravel\Scout\Searchable;
    
    #[MeilisearchFilterableAttributes(['id', 'name', 'email'])]
    #[MeilisearchSortableAttributes(['created_at'])]
    public function toSearchableArray()
    {
        //
    }
}
```

```php
class Flight extends \Illuminate\Database\Eloquent\Model
{
    use \Laravel\Scout\Searchable;
    
    #[MeilisearchFilterableAttributes(['id', 'destination'])]
    #[MeilisearchSortableAttributes(['updated_at'])]
    public function toSearchableArray()
    {
        //
    }
}
```

Just enumerate searchable classes in `config/scout.php`:

```php
use App\Models\User;
use App\Models\Flight;
 
'meilisearch' => [
    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'key' => env('MEILISEARCH_KEY', null),
    'searchable' => [User::class, Flight::class],
],
```

## Console

Use `meilisearch:rebuild` command to completely rebuild Meilisearch index.  