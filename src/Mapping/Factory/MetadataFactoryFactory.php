<?php
/*
 * Copyright (c) GOTO Hidenori <hidenorigoto@gmail.com>, KUBO Atsuhiro <kubo@iteman.jp>, and contributors,
 * All rights reserved.
 *
 * This file is part of PHPMentorsValidatorBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\ValidatorBundle\Mapping\Factory;

use Doctrine\Common\Annotations\Reader;
use PHPMentors\ValidatorBundle\Mapping\Loader\XmlFileLoader;
use PHPMentors\ValidatorBundle\Mapping\Loader\XmlFilesLoader;
use PHPMentors\ValidatorBundle\Mapping\Loader\YamlFileLoader;
use PHPMentors\ValidatorBundle\Mapping\Loader\YamlFilesLoader;
use Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Loader\LoaderChain;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ValidatorBuilder;

/**
 * @since Class available since Release 1.0.0
 */
class MetadataFactoryFactory
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var array
     */
    private $constraintNamespaces;

    /**
     * @var CacheInterface
     */
    private $metadataCache;

    /**
     * @var string[]
     */
    private $methodMappings = array();

    /**
     * @var string[]
     */
    private $xmlMappings = array();

    /**
     * @var string[]
     */
    private $yamlMappings = array();

    /**
     * @param Reader $annotationReader
     */
    public function setAnnotationReader(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param array $constraintNamespaces
     */
    public function setConstraintNamespaces(array $constraintNamespaces)
    {
        $this->constraintNamespaces = $constraintNamespaces;
    }

    /**
     * @param string $xmlMapping
     */
    public function addXmlMapping($xmlMapping)
    {
        $this->xmlMappings[] = $xmlMapping;
    }

    /**
     * @param string[] $xmlMappings
     */
    public function addXmlMappings(array $xmlMappings)
    {
        $this->xmlMappings = array_merge($this->xmlMappings, $xmlMappings);
    }

    /**
     * @param string $yamlMapping
     */
    public function addYamlMapping($yamlMapping)
    {
        $this->yamlMappings[] = $yamlMapping;
    }

    /**
     * @param string[] $yamlMappings
     */
    public function addYamlMappings(array $yamlMappings)
    {
        $this->yamlMappings = array_merge($this->yamlMappings, $yamlMappings);
    }

    /**
     * @param string $methodMapping
     */
    public function addMethodMapping($methodMapping)
    {
        $this->methodMappings[] = $methodMapping;
    }

    /**
     * @param string[] $methodMappings
     */
    public function addMethodMappings(array $methodMappings)
    {
        $this->methodMappings = array_merge($this->methodMappings, $methodMappings);
    }

    /**
     * @param CacheInterface $cache
     */
    public function setMetadataCache(CacheInterface $cache)
    {
        $this->metadataCache = $cache;
    }

    /**
     * @return MetadataFactoryInterface
     *
     * @see ValidatorBuilder::getValidator()
     */
    public function create()
    {
        $loaders = array();

        if (count($this->xmlMappings) > 1) {
            $loaders[] = new XmlFilesLoader($this->xmlMappings, $this->constraintNamespaces);
        } elseif (1 === count($this->xmlMappings)) {
            $loaders[] = new XmlFileLoader($this->xmlMappings[0], $this->constraintNamespaces);
        }

        if (count($this->yamlMappings) > 1) {
            $loaders[] = new YamlFilesLoader($this->yamlMappings, $this->constraintNamespaces);
        } elseif (1 === count($this->yamlMappings)) {
            $loaders[] = new YamlFileLoader($this->yamlMappings[0], $this->constraintNamespaces);
        }

        foreach ($this->methodMappings as $methodName) {
            $loaders[] = new StaticMethodLoader($methodName);
        }

        if ($this->annotationReader) {
            $loaders[] = new AnnotationLoader($this->annotationReader);
        }

        $loader = null;

        if (count($loaders) > 1) {
            $loader = new LoaderChain($loaders);
        } elseif (1 === count($loaders)) {
            $loader = $loaders[0];
        }

        return new LazyLoadingMetadataFactory($loader, $this->metadataCache);
    }
}
