# Change Log

## v1.1.2

- Added Laravel 13 support
- Modernized PHPUnit configuration (migrated to PHPUnit 12)
- Updated development dependencies
- Fixed minor PHPStan type declaration error in `Repository` interface

## v1.1.1

- Added Laravel 12 support
- Added PHP 8.4 compatibility
- Added global scope support
- Added option arguments to search
- Updated test patterns
- Moved common logic to traits

## v1.0

- `Torann\LaravelRepository\Contracts\RepositoryContract` renamed to `Torann\LaravelRepository\Contracts\Repository`
- `Torann\LaravelRepository\Repositories\AbstractRepository` renamed to `Torann\LaravelRepository\Repository`
- `Torann\LaravelRepository\Traits\Cacheable` renamed to `Torann\LaravelRepository\Concerns\Cacheable`
- Updated existing and added missing type declarations
- `orderBy()` can only be applied once (intended limitation)
