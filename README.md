# psr4-wildcard-composer-plugin
Adds a parser to enable composer, to be used with wildcards

### How to use

Both glob and sprintf are used to dynamically replace content of the generated autoload file:

- Use GLOB Braces to define folder dynamically in your composer.json (Epx.: "/modules/{*Domain,*Module}/{*}/src")
- Use %s of sprintf to match findings of GLOB to your file path (Exp.: "My\\Namespace\\%s\\%s\\")
- GLOB is case in/sensitive on linux/windows
- Also you may use argument switching, but that is not recommended (Exp.: "My\\Namespace\\%2$s\\%1$s\\")

### Example

composer.json:

````json
{
  "autoload": {
    "psr-4": {
      "My\\Namespace\\%s\\%s\\": "modules/{*Domain,*Module}/{*}/src"
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

AdvancedWildcards + FileSystem transforms to:

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
- You will get wired results, if folders do not exist 
