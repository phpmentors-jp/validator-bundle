<?php
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
                        $metadataFactoryFactoryDefinition->addMethodCall('addMethodMapping', $arguments);
                        break;
                    case 'addXmlMappings':
                        $metadataFactoryFactoryDefinition->addMethodCall('setXmlMappings', $arguments);
                        break;
                    case 'addYamlMappings':
                        $metadataFactoryFactoryDefinition->addMethodCall('setYamlMappings', $arguments);
                        break;
                    case 'enableAnnotationMapping':
                        $metadataFactoryFactoryDefinition->addMethodCall('setAnnotationReader', array(clone $arguments[0]));
                        break;
                    case 'setApiVersion':
                        $metadataFactoryFactoryDefinition->addMethodCall('setApiVersion', $arguments);
                        break;
                    case 'setMetadataCache':
                        $metadataFactoryFactoryDefinition->addMethodCall('setMetadataCache', array(clone $arguments[0]));
                        break;
                    default:
                        break;
                }
            }

            $validatorBuilderDefinition->setMethodCalls(array_merge(
                array_filter($methodCalls, function ($methodCall) {
                    list($method, $arguments) = $methodCall;

                    return !in_array($method, array('addMethodMapping', 'addXmlMappings', 'addYamlMappings', 'enableAnnotationMapping'));
                }),
                array(array('setMetadataFactory', array(new Reference('phpmentors_validator.metadata_factory'))))
            ));
        }
    }
}
