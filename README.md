# LunaQL PHP Client

This is a PHP client for the [LunaQL](https://lunaql.com) NoSQL database.

## Example

```php
use LunaQL\Database;
use LunaQL\Config\DatabaseConfig;
use LunaQL\Builder\RelationshipBuilder;

$db = new Database(new DatabaseConfig(
    endpoint: "<endpoint>",
    token: '<token>'
));

$objectIDs = $db->query()
    ->from("users")
    ->limit(1)
    ->select(["_fk"])
    ->list("_fk");

$results = $db->query()
    ->from("users")
    ->where("_fk", "in", $objectIDs)
    ->hasMany("tasks", function (RelationshipBuilder $q) {
        $q->where('user_id', '=', '$._id')->orderBy('created_at', 'asc');
    })
    ->fetch();

var_dump($results);
```

## Todo
- [ ] Add tests
- [ ] Implement error handling
- [ ] Implement more query methods

Security
-------

If you discover any security related issues, please email donaldpakkies@gmail.com instead of using the issue tracker.

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.
