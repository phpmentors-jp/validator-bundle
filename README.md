# PHPMentorsValidatorBundle

Validation enhancements for Symfony applications

[![Total Downloads](https://poser.pugx.org/phpmentors/validator-bundle/downloads.png)](https://packagist.org/packages/phpmentors/validator-bundle)
[![Latest Stable Version](https://poser.pugx.org/phpmentors/validator-bundle/v/stable.png)](https://packagist.org/packages/phpmentors/validator-bundle)
[![Latest Unstable Version](https://poser.pugx.org/phpmentors/validator-bundle/v/unstable.png)](https://packagist.org/packages/phpmentors/validator-bundle)
[![Build Status](https://travis-ci.org/phpmentors-jp/validator-bundle.svg?branch=master)](https://travis-ci.org/phpmentors-jp/validator-bundle)

## Features

* Namespace alias configuration
* Constraints
  * AtLeastOneOf
  * Range
  * ServiceCallback

## Installation

`PHPMentorsValidatorBundle` can be installed using [Composer](http://getcomposer.org/).

First, add the dependency to `phpmentors/validator-bundle` into your `composer.json` file as the following:

**Stable version**:

```
composer require phpmentors/validator-bundle "1.1.*"
```

**Development version**:

```
composer require phpmentors/validator-bundle "~1.2@dev"
```

Second, add `PHPMentorsValidatorBundle` into your bundles to register in `AppKernel::registerBundles()` as the following:

```php
...
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

## Support

If you find a bug or have a question, or want to request a feature, create an issue or pull request for it on [Issues](https://github.com/phpmentors-jp/validator-bundle/issues).

## Copyright

Copyright (c) GOTO Hidenori, KUBO Atsuhiro, and contributors, All rights reserved.

## License

[The BSD 2-Clause License](http://opensource.org/licenses/BSD-2-Clause)
