# Symfony2 Extra Constraint/Validator Set

## Installation

via Composer/Packagist

```
# in composer.json
"phpmentors/validator-bundle": "~0.1"

# or composer command
$ php composer.phar requure phpmentors/validator-bundle
```

## ServiceCallback

ServiceCallback constraint and ServiceCallbackValidator enables to validate a value using a method of a service registered in Symfony's service container.

```
/**
 * @ServiceCallback(service="mydomain.specification.email_limit",method="isSatisfiedBy",
    message="email limit error.", errorPath="emailMax", groups={"user_check"})
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
