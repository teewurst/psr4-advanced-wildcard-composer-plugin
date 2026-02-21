# PSR4 Advanced Wildcard Composer Plugin

Adds a parser so Composer can handle wildcards in your autoload configuration. Because listing every single file by hand is so 2015.

[![CI](https://github.com/teewurst/psr4-advanced-wildcard-composer-plugin/actions/workflows/ci.yml/badge.svg)](https://github.com/teewurst/psr4-advanced-wildcard-composer-plugin/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/teewurst/psr4-advanced-wildcard-composer-plugin/branch/master/graph/badge.svg)](https://codecov.io/gh/teewurst/psr4-advanced-wildcard-composer-plugin)

## Installation

```bash
composer require teewurst/psr4-advanced-wildcard-composer-plugin
```

## How it works

Glob patterns and `sprintf` team up to dynamically replace content in the generated autoload file:

- **GLOB braces** define folders dynamically (e.g. `"/modules/{*Domain,*Module}/{*}/src"`)
- **`%s` placeholders** in namespaces match the GLOB findings (e.g. `"My\\Namespace\\%s\\%s\\"`)
- GLOB is case-insensitive on Linux and Windows
- Argument switching works too, though we don't recommend it (e.g. `"My\\Namespace\\%2$s\\%1$s\\"`)
- **IDEs** can't handle advanced wildcards (no auto-complete, no namespace recognition, etc.)
  - Solution: run Composer in `--dev` mode — it generates a `composer.development.json` with all wildcards resolved. Your IDE will love you for it.

### Configuration options

1. **(Recommended)** Add wildcards to `extra.teewurst/psr4-advanced-wildcard-composer-plugin.autoload.psr-4`
2. Set `extra.teewurst/psr4-advanced-wildcard-composer-plugin` to a truthy value and use wildcards in your default `autoload.psr-4`
3. **File autoload wildcards**: Use patterns in `autoload.files` to include matching files automatically (e.g. `"app/Helpers/{*}.php"` instead of listing each file)

### Example

**composer.json:**

```json
{
  "extra": {
    "teewurst/psr4-advanced-wildcard-composer-plugin": {
      "autoload": {
        "psr-4": {
          "My\\Namespace\\%s\\%s\\": "modules/{*Domain,*Module}/{*}/src"
        },
        "files": [
          "app/Helpers/{*}.php"
        ]
      },
      "autoload-dev": {
        "psr-4": {
          "My\\Namespace\\test\\%s\\": "tests/{*}/src"
        }
      }
    }
  }
}
```

**File structure:**

```
|- composer.json
|- modules
   |- BusinessDomain
      |- Calculation
         |- src
      |- Listener
         |- src
   |- DataModule
      |- AWS
         |- src
      |- Mysql
         |- src
   |- SomethingElse
```

**Equivalent to:**

```json
{
  "autoload": {
    "psr-4": {
      "My\\Namespace\\BusinessDomain\\Calculation\\": "modules/BusinessDomain/Calculation/src",
      "My\\Namespace\\BusinessDomain\\Listener\\": "modules/BusinessDomain/Listener/src",
      "My\\Namespace\\DataModule\\AWS\\": "modules/DataModule/AWS/src",
      "My\\Namespace\\DataModule\\Mysql\\": "modules/DataModule/Mysql/src"
    }
  }
}
```

## Limitations & Performance

A few things to keep in mind:

- **Glob/IO/Performance**: Yes, `dump-autoload` will take a bit longer. That's the price of flexibility.
- **One folder level per replacement**: The plugin is limited to one wildcard level per namespace segment. Adding more would get … interesting.
- **Non-existent folders**: You'll get weird results if folders don't exist. Create them first.

## Development (Docker)

A Docker Compose setup is included so you don't need PHP or Composer installed locally.

```bash
# Build the image (PHP 8.5 by default)
docker compose build

# Install dependencies
docker compose run --rm php install

# Run tests
docker compose run --rm php test

# Generate coverage report (coverage.xml)
docker compose run --rm php coverage

# Validate 100% coverage
docker compose run --rm php coverage:check

# Run static analysis (PHPStan)
docker compose run --rm php analyse

# Any other Composer command
docker compose run --rm php update
docker compose run --rm php require some/package
```

To use a different PHP version, set `PHP_VERSION` before building:

```bash
PHP_VERSION=7.4 docker compose build
PHP_VERSION=7.4 docker compose run --rm php test
```

## Contributing

1. Create a dummy repository locally and add the plugin as a path dependency:

```json
"repositories": [
  {
    "type": "path",
    "version": "dev-[branch_name]",
    "url": "[path_to_local_wildcard_plugin]/psr4-advanced-wildcard-composer-plugin"
  }
]
```

2. Run `composer require teewurst/psr4-advanced-wildcard-composer-plugin` in your dummy repo
3. Test your changes with `composer dump-autoload`
4. **xDebug users**: Run `export COMPOSER_ALLOW_XDEBUG=1` in your terminal session
5. All contributions must pass `composer test` and `composer analyse`

### CI & Quality

This project uses GitHub Actions for CI. Every push and pull request runs:
- **Tests** (PHPUnit) on PHP 7.4, 8.2, 8.3, 8.4, and 8.5
- **Static analysis** (PHPStan, level 4)
- **Code coverage** — the build fails if coverage drops below 100%

To require a passing build before merging, enable branch protection in your repo settings: *Settings → Branches → Add rule* and require the "Tests & Coverage" check.
