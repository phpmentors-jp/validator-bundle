<?php
/*
 * Copyright (c) 2014 GOTO Hidenori <hidenorigoto@gmail.com>,
 *               2015 KUBO Atsuhiro <kubo@iteman.jp>,
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
use Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Loader\LoaderChain;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ValidatorBuilder;

use PHPMentors\ValidatorBundle\Mapping\Loader\XmlFileLoader;
use PHPMentors\ValidatorBundle\Mapping\Loader\XmlFilesLoader;
use PHPMentors\ValidatorBundle\Mapping\Loader\YamlFileLoader;
use PHPMentors\ValidatorBundle\Mapping\Loader\YamlFilesLoader;

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
     * @var int
     */
    private $apiVersion;

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
     * @param int $apiVersion
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    /**
     * @param array $constraintNamespaces
     */
    public function setConstraintNamespaces(array $constraintNamespaces)
    {
        $this->constraintNamespaces = $constraintNamespaces;
    }

    /**
     * @param string[] $xmlMappings
     */
    public function setXmlMappings(array $xmlMappings)
    {
        $this->xmlMappings = $xmlMappings;
    }

    /**
     * @param string[] $yamlMappings
     */
    public function setYamlMappings(array $yamlMappings)
    {
        $this->yamlMappings = $yamlMappings;
    }

    /**
     * @param string $methodName
     */
    public function addMethodMapping($methodName)
    {
        $this->methodMappings[] = $methodName;
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

        if (Validation::API_VERSION_2_5 === $this->apiVersion) {
            $metadataFactory = new LazyLoadingMetadataFactory($loader, $this->metadataCache);
        } else {
            $metadataFactory = new ClassMetadataFactory($loader, $this->metadataCache);
        }

        return $metadataFactory;
    }
}
