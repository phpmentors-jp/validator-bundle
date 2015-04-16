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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @since Class available since Release 1.0.0
 */
class AppKernel extends Kernel
{
    /**
     * @var \Closure
     */
    private $config;

    /**
     * {@inheritDoc}
     */
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new PHPMentors\PageflowerBundle\PHPMentorsPageflowerBundle(),
            new PHPMentors\ValidatorBundle\PHPMentorsValidatorBundle(),
            new PHPMentors\ValidatorBundle\Functional\Bundle\TestBundle\TestBundle(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');

        if ($this->config instanceof \Closure) {
            $loader->load($this->config);
        }
    }

    /**
     * @param \Closure $config
     */
    public function setConfig(\Closure $config)
    {
        $this->config = $config;
    }
}
