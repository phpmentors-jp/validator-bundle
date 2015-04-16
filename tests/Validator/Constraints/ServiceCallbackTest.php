<?php
/**
 * PHP version 5.5
 *
 * Copyright (c) 2014 GOTO Hidenori <hidenorigoto@gmail.com>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPMentors_ValidatorBundle
 * @copyright  2014 GOTO Hidenori <hidenorigoto@gmail.com>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      File available since Release 0.1.0
 */
namespace PHPMentors\ValidatorBundle\Validator\Constraints;

/**
 * @package    PHPMentors_ValidatorBundle
 * @copyright  2014 GOTO Hidenori <hidenorigoto@gmail.com>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      File available since Release 0.1.0
 */
class ServiceCallbackTest extends \PHPUnit_Framework_TestCase
{
    const SERVICE_NAME   = 'test.bar_service';
    const SERVICE_METHOD = 'bazMethod';
    /**
     * @test
     */
    public function validateSuccess()
    {
        $constraint = new ServiceCallback(array(
            'service' => self::SERVICE_NAME,
            'method' =>  self::SERVICE_METHOD,
        ));

        $this->assertThat($constraint, $this->isInstanceOf('PHPMentors\ValidatorBundle\Validator\Constraints\ServiceCallback'));
        $this->assertThat($constraint->validatedBy(), $this->logicalNot($this->equalTo(null)));
        $this->assertThat($constraint->getTargets(), $this->logicalNot($this->equalTo(null)));
    }
}
