<?php
/*
 * Copyright (c) 2014 GOTO Hidenori <hidenorigoto@gmail.com>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsValidatorBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

error_reporting(E_ALL);

$loader = require dirname(__DIR__).'/vendor/autoload.php'; /* @var $loader \Composer\Autoload\ClassLoader */

Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
