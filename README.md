# psr4-advanced-wildcard-composer-plugin
Adds a parser to enable composer, to be used with wildcards
[![codecov](https://codecov.io/gh/teewurst/psr4-advanced-wildcard-composer-plugin/branch/master/graph/badge.svg)](https://codecov.io/gh/teewurst/psr4-advanced-wildcard-composer-plugin)

### How to use

Both glob and sprintf are used to dynamically replace content of the generated autoload file:

- Use GLOB Braces to define folder dynamically in your composer.json (Epx.: `"/modules/{*Domain,*Module}/{*}/src"`)
- Use %s of sprintf to match findings of GLOB to your file path (Exp.: `"My\\Namespace\\%s\\%s\\"`)
- GLOB is case in/sensitive on linux/windows
- Also you may use argument switching, but that is not recommended (Exp.: `"My\\Namespace\\%2$s\\%1$s\\"`)
- IDEs cant handle Advanced Wildcards in composer.json (File creation, namespace auto-complete etc.)
    - if you run in --dev mode, it will generate a composer.development.json at the same location
    - it is a exact copy of composer.json, but resolved wildcards
- There are two ways of configuration in your composer.json (equivalent to autoload-dev)
    - (Recommended) You add your wildcards to ``extra.teewurst/psr4-advanced-wildcard-composer-plugin.autoload.psr-4`` (see example)
    - You set ``extra.teewurst/psr4-advanced-wildcard-composer-plugin`` to a truthy value and set your namespaces in your default ``autoload.psr-4``

### Example

composer.json:

````json
{
  "extra": {
    "teewurst/psr4-advanced-wildcard-composer-plugin": {
      "autoload": {
        "psr-4": {
          "My\\Namespace\\%s\\%s\\": "modules/{*Domain,*Module}/{*}/src"
        }
      },
      "autoload-dev": {
        "psr-4": {
          /* Namespaces */
        }
      }
    }
  }
}
````

FileSystem:

`````
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
`````

AdvancedWildcards + FileSystem is equivalent:

````json

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
````

### Limitations and Performance

Be aware that...

- Glob/IO and performance? No, No, No... dump-autoload will take a bit longer
- This plugin is limited to one folder level per namespace replacement (Oh boy, it would escalate quickly)
- You will get weired results, if folders do not exist 

## Contribute

- Create any dummy repository locally, setup composer and add to that composer.json:

````json
    "repositories": [
      {
        "type": "path",
        "version": "dev-[branch_name]",
        "url": "[path_to_local_wildcard_plugin]/psr4-advanced-wildcard-composer-plugin"
      },
    ],
````
- in dummy repository, fire `composer require teewurst/psr4-advanced-wildcard-composer-plugin`
- in dummy repository, you can execute the code by `composer dump-autoload`
- to enable xDebug, you have to fire `export COMPOSER_ALLOW_XDEBUG=1` (*session* env variable = execute in every terminal)
- addition needs to pass `composer test` and `composer analyse`
