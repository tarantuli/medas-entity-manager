# Relations

## Many-to-many relations

In this example, we will connect one movie with zero or more genres.

The `Movie` class:

```php
#[Entity('movies')]
class Movie implements HasId
{
    #[Id, IsGeneratedValue]
    public int $id;

    #[IsUnique]
    public string $title;
}
```

The `Genre` class:

```php
#[Entity('genres')]
class Genre implements HasId
{
    #[Id, IsGeneratedValue]
    public int $id;

    #[IsUnique]
    public string $name;
}
```

We need a collection class, which can hold a number of `Genre` objects:

```php
/**
 * @extends RecordCollection<Genre>
 */
#[EntityCollection(Genre::class)]
class GenreCollection extends RecordCollection
{
}
```

It must be tagged with @class(Medas\EntityManager\Attributes\EntityCollection) and it must implement @class(
Medas\Core\Interfaces\ManagedCollection). @class(Medas\StorageManager\Entities\RecordCollection) is a good base class to
extend.

Simply use this collection class as the type of the `genres` property in `Movies`:

```php
#[Entity('movies')]
class Movie implements HasId
{
    #[Id, IsGeneratedValue]
    public int $id;

    #[IsUnique]
    public string $title;

    public GenreCollection $genres;
}
```

The storage manager will have enough information to generate the necessary migrations, and to store and fetch connected
genres.
