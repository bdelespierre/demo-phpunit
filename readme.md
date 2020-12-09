# PHPUnit Demo

## Requirements

+ PHP 7.3+
+ [Composer](https://getcomposer.org/)
+ [Xdebug](https://xdebug.org/)

## install

```BASH
git clone git@github.com:bdelespierre/demo-phpunit.git
cd demo-phpunit
composer install
```

## Run tests (with coverage)

```BASH
vendor/bin/phpunit --coverage-text
```

## Exercise

Implement the following method:

```PHP
class PersonRepository
{
    /**
     * Get all the person whose name match the given search criteria (case-insensitive.)
     *
     * @param  string $search The search criteria
     *
     * @throws \InvalidArgumentException when search string is empty
     *
     * @return \App\Entities\Person[]
     */
    public function search(string $search): array
    {
        // your code here.
    }
}
```

Then write corresponding tests to keep coverage of PersonRepository at 100%.
