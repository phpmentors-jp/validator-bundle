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

namespace PHPMentors\ValidatorBundle\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class AtLeastOneOf extends Constraint
{
    /**
     * @var string[]
     */
    public $properties;

    /**
     * @var string[]
     */
    public $errorPaths;

    /**
     * @var string
     */
    public $message = 'At least one of {{ properties }} is required.';

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'properties';
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('properties');
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
