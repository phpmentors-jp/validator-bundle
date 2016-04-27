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

namespace PHPMentors\ValidatorBundle\Fixtures;

class AtLeastOneOfEntity
{
    /**
     * @param mixed
     */
    private $a;

    /**
     * @param mixed
     */
    private $b;

    /**
     * @param mixed
     */
    private $c;

    /**
     * @param mixed $a
     */
    public function setA($a)
    {
        $this->a = $a;
    }

    /**
     * @param mixed $b
     */
    public function setB($b)
    {
        $this->b = $b;
    }

    /**
     * @param mixed $c
     */
    public function setC($c)
    {
        $this->c = $c;
    }
}
