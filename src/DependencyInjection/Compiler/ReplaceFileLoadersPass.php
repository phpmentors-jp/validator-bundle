<?php
/*
 * Copyright (c) 2015 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsValidatorBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\ValidatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ReplaceFileLoadersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('validator.mapping.loader.xml_files_loader.mapping_files') && $container->hasParameter('validator.mapping.loader.yaml_files_loader.mapping_files')) {
            $xmlFilesLoaderDefinition = $container->getDefinition('phpmentors_validator.xml_files_loader');
            $arguments = $xmlFilesLoaderDefinition->getArguments();
            $arguments[0] = '%validator.mapping.loader.xml_files_loader.mapping_files%';
            $xmlFilesLoaderDefinition->setArguments($arguments);
            $container->setDefinition('validator.mapping.loader.xml_files_loader', $xmlFilesLoaderDefinition);
            $container->removeDefinition('phpmentors_validator.xml_files_loader');

            $yamlFilesLoaderDefinition = $container->getDefinition('phpmentors_validator.yaml_files_loader');
            $arguments = $yamlFilesLoaderDefinition->getArguments();
            $arguments[0] = '%validator.mapping.loader.yaml_files_loader.mapping_files%';
            $yamlFilesLoaderDefinition->setArguments($arguments);
            $container->setDefinition('validator.mapping.loader.yaml_files_loader', $yamlFilesLoaderDefinition);
            $container->removeDefinition('phpmentors_validator.yaml_files_loader');
        } else {
            $validatorBuilderDefinition = $container->getDefinition('validator.builder');
            $metadataFactoryFactoryDefinition = $container->getDefinition('phpmentors_validator.metadata_factory_factory');

            $methodCalls = $validatorBuilderDefinition->getMethodCalls();
            foreach ($methodCalls as $methodCall) {
                list($method, $arguments) = $methodCall;

                switch ($method) {
                case 'addMethodMapping':
                case 'addMethodMappings':
                case 'addXmlMapping':
                case 'addXmlMappings':
                case 'addYamlMapping':
                case 'addYamlMappings':
                    $metadataFactoryFactoryDefinition->addMethodCall($method, $arguments);
                    break;
                case 'enableAnnotationMapping':
                    $metadataFactoryFactoryDefinition->addMethodCall('setAnnotationReader', array(clone $arguments[0]));
                    break;
                case 'setMetadataCache':
                    $metadataFactoryFactoryDefinition->addMethodCall($method, array(clone $arguments[0]));
                    break;
                default:
                    break;
                }
            }

            $validatorBuilderDefinition->setMethodCalls(array_merge(
                array_filter($methodCalls, function ($methodCall) {
                    list($method, $arguments) = $methodCall;

                    return !in_array($method, array('addMethodMapping', 'addMethodMappings', 'addXmlMapping', 'addXmlMappings', 'addYamlMapping', 'addYamlMappings', 'enableAnnotationMapping', 'setMetadataCache'));
                }),
                array(array('setMetadataFactory', array(new Reference('phpmentors_validator.metadata_factory'))))
            ));
        }
    }
}
