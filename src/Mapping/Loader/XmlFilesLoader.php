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

use Symfony\Component\Validator\Mapping\Loader\FilesLoader;

class XmlFilesLoader extends FilesLoader
{
    /**
     * @var array
     */
    private $constraintNamespaces;

    /**
     * {@inheritDoc}
     *
     * @param array $constraintNamespaces
     */
    public function __construct(array $paths, array $constraintNamespaces)
    {
        $this->constraintNamespaces = $constraintNamespaces;

        parent::__construct($paths);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFileLoaderInstance($file)
    {
        return new XmlFileLoader($file, $this->constraintNamespaces);
    }
}
