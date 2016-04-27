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

namespace PHPMentors\ValidatorBundle\Constraints;

class ServiceCallbackTest extends \PHPUnit_Framework_TestCase
{
    const SERVICE_NAME = 'test.bar_service';
    const SERVICE_METHOD = 'bazMethod';
    /**
     * @test
     */
    public function validateSuccess()
    {
        $constraint = new ServiceCallback(array(
            'service' => self::SERVICE_NAME,
            'method' => self::SERVICE_METHOD,
        ));

        $this->assertThat($constraint, $this->isInstanceOf('PHPMentors\ValidatorBundle\Constraints\ServiceCallback'));
        $this->assertThat($constraint->validatedBy(), $this->logicalNot($this->equalTo(null)));
        $this->assertThat($constraint->getTargets(), $this->logicalNot($this->equalTo(null)));
    }
}
