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

/**
 * @since Class available since Release 1.2.0
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Range extends \Symfony\Component\Validator\Constraints\Range
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'phpmentors_validator.range_validator';
    }
}
