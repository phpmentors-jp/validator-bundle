# Symfony2 Extra Constraint/Validator Set

[![Build Status](https://travis-ci.org/phpmentors-jp/validator-bundle.svg?branch=master)](https://travis-ci.org/phpmentors-jp/validator-bundle)

## Installation

via Composer/Packagist

```
# in composer.json
"phpmentors/validator-bundle": "~0.1"

# or composer command
$ php composer.phar require phpmentors/validator-bundle
```

and add a line for loading this bundle in AppKernel.php

```
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new PHPMentors\ValidatorBundle\PHPMentorsValidatorBundle(),
        );

        ...
```


## ServiceCallback

ServiceCallback constraint and ServiceCallbackValidator enables to validate a value using a method of a service registered in Symfony's service container.

```
/**
 * @ServiceCallback(service="mydomain.specification.email_limit", method="isSatisfiedBy",
    message="email limit error.", errorPath="emailMax")
 */
class User
{

```

```
/**
 * @DI\Service("mydomain.specification.email_limit")
 */
class EmailLimit
{
    public function isSatisfiedBy($target)
    {
        return $target->getEmailsCount() > 5;  // Sometimes exceed the limit.
    }
}
```

### Return value and validation result

* When the service method returns `false`, the validation fails.
* When any other, the validation passes.

### Options

| Option               | Description                                                         |
| -------------------- |---------------------------------------------------------------------|
| service              | Specify service name                                                |
| method               | Specify method name to be called                                    |
| message              | (Optional) Error message                                            |
| errorPath            | (Optional) PropertyPath for which error message displayed in        |
