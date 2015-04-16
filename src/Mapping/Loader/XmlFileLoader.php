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

namespace PHPMentors\ValidatorBundle\Mapping\Loader;

class XmlFileLoader extends \Symfony\Component\Validator\Mapping\Loader\XmlFileLoader
{
    /**
     * {@inheritDoc}
     *
     * @param array $constraintNamespaces
     */
    public function __construct($file, array $constraintNamespaces)
    {
        parent::__construct($file);

        foreach ($constraintNamespaces as $alias => $namespace) {
            $this->addNamespaceAlias($alias, $namespace);
        }
    }
}