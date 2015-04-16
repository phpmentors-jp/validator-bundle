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

namespace PHPMentors\ValidatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private static $defaultConstraintNamespaces = array(
        'PHPMentors' => 'PHPMentors\\ValidatorBundle\\Constraints\\',
    );

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $defaultConstraintNamespaces = self::$defaultConstraintNamespaces;
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('phpmentors_validator')
            ->children()
                ->arrayNode('constraint')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('namespaces')
                            ->useAttributeAsKey('alias')
                            ->defaultValue(self::$defaultConstraintNamespaces)
                            ->validate()
                                ->always(function (array $v) use ($defaultConstraintNamespaces) {
                                    foreach ($v as $key => $value) {
                                        if ($key === null || strlen($key) == 0) {
                                            throw new \InvalidArgumentException(sprintf('A key for "%s" should be non-empty string.', 'phpmentors_validator.constraint.namespaces'));
                                        }
                                    }

                                    foreach ($defaultConstraintNamespaces as $key => $value) {
                                        if (!array_key_exists($key, $v)) {
                                            $v[$key] = $value;
                                        }
                                    }

                                    return $v;
                                })
                            ->end()
                            ->prototype('scalar')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
