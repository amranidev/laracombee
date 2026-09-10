# CHANGELOG

## v1.0.0

### Added

- Laravel 13 and Orchestra Testbench 11 support, with a Laravel 13 development lockfile.
- Optional SDK client injection and a configurable `ModelMapper` for identifiers, values, and schema properties.
- Configurable client creation through `LaracombeeConnector` for multiple databases.
- Facade alias discovery and container resolution by `Laracombee::class`.
- Regression tests for SDK execution and failures, model mapping, Artisan commands, database chunking, and generated clients.
- CI coverage for Laravel 12 on PHP 8.2 and Laravel 13 on PHP 8.3–8.5.

### Changed

- Preserve original exceptions when rejecting promises; error callbacks now receive exception objects instead of strings.
- Default to HTTPS and read credentials from `RECOMBEE_DATABASE` and `RECOMBEE_TOKEN` environment variables.
- Seed records using database-level chunks and stop with a failure exit code when a batch fails.
- Validate command catalog types, configured models, column syntax, and positive chunk sizes.
- Generate lightweight client subclasses through Laravel's generator, respecting application paths and preventing overwrites.
- Document deferred synchronous execution, client injection, mapping, testing, and migration considerations.

### Fixed

- Use the package facade explicitly inside commands instead of depending on a global alias supplied only by tests.
- Use Eloquent primary keys for model identifiers instead of assuming an `id` column.
- Pass the SDK's `cascadeCreate` option when merging users.
- Eliminate the missing constructor argument and duplicated implementation in generated clients.
- Return nonzero command exit codes for validation and execution failures instead of exiting the process or swallowing errors.
- Correct the CI matrix runner configuration and ignore PHPUnit's cache directory.

## v0.2.1 (2022-08-17)

### Fixed

- Upgrade recombee SDK.
- Update Laracombee client constructor.
- Fix tests.

## v0.1.37 (2019-04-21)

### Fixed

- Fix bug in `addDetailView` param.

## v0.1.36 (2019-04-21)

### Fixed

- Add an empty array to the defaults params.

## v0.1.35 (2019-04-21)

### Fixed

- Make `addDetailView` consistent with the api.

## v0.1.31 (2019-01-16)

### Added

- Ability to create custom laracombee class with artisan command.

## v0.1.30 (2019-01-13)

### Added

- Ability to connect multiple databases.

## v0.1.28 (2018-12-28)

### Fixed

- Fix bug [#37](https://github.com/amranidev/laracombee/issues/37)

## v0.1.27 (2018-12-18)

### Fixed

- Fix, recommned users to user [7958844](https://github.com/amranidev/laracombee/commit/795884494ff0a83d4191ef2cd50ceb596eee4676)

## v0.1.26 (2018-12-17)

### Added

- Ability to specify the default item/user classes in laracombee config file.
- Add recombee ViewPortion, see [Recombee view portions api](https://docs.recombee.com/api.html#view-portions).

### Removed

- Remove the class tag in artisan commands.

### Fixed

- Fix bug in the `seed` command.

### Improved

- Set timeout during each request.

## v0.1.20 (2018-12-10)

### Added

- Ability to specify the http protocol (http/https).
- Craete new command, Reset database, `php artisan laracimbee:reset`.

### Fixed

- Fix bug in the `seed` command.

### Improved

- Set timeout during each request.

## v0.1.15 (2018-12-03)

### Added

- Add series to the API [Recombee series](https://docs.recombee.com/api.html#series).

## v0.1.1 (2018-11-01)

### Initial Release :tada:
