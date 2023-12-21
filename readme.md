# Laravel CRUD Generator Package


### Documentation, Installation, and Usage Instructions


## Installation



Via Composer

``` bash
$ composer require crud/crudgenerator
```


``` bash
$ php artisan vendor:publish --tag=crudgenerator
```
### added in providers

```
$ crud\crudGenerator\CrudGeneratorServiceProvider::class,

```
### Usage Instructions
```
php artisan curd:crudgenerator ExampleName
