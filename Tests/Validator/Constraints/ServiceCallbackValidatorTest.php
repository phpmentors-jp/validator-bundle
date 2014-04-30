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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * @package    PHPMentors_ValidatorBundle
 * @copyright  2014 GOTO Hidenori <hidenorigoto@gmail.com>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      File available since Release 0.1.0
 */
class ServiceCallbackValidatorTest extends \PHPUnit_Framework_TestCase
{
    const SERVICE_NAME   = 'test.bar_service';
    const SERVICE_METHOD = 'bazMethod';
    const ERROR_MESSAGE  = 'error message';
    const ERROR_PATH     = 'someProperty';

    /**
     * @var ServiceCallbackValidator
     */
    private $SUT;
    /**
     * @var ExecutionContextInterface
     */
    private $executionContext;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var BarService
     */
    private $service;

    /**
     * @test
     */
    public function validateSuccess()
    {
        $this->mockService();

        $constraint = new ServiceCallback(array(
            'service' => self::SERVICE_NAME,
            'method' =>  self::SERVICE_METHOD,
            'message' => self::ERROR_MESSAGE,
        ));

        $entity = new \stdClass();

        $this->service->expects($this->once())
            ->method(self::SERVICE_METHOD)
            ->with($entity)
            ->will($this->returnValue(true));

        $this->executionContext->expects($this->never())
            ->method('addViolation');
        $this->executionContext->expects($this->never())
            ->method('addViolationAt');

        $this->SUT->validate($entity, $constraint);
    }

    /**
     * @test
     */
    public function validateFail()
    {
        $this->mockService();

        $constraint = new ServiceCallback(array(
            'service' => self::SERVICE_NAME,
            'method' =>  self::SERVICE_METHOD,
            'message' => self::ERROR_MESSAGE,
        ));

        $entity = new \stdClass();

        $this->service->expects($this->once())
            ->method(self::SERVICE_METHOD)
            ->with($entity)
            ->will($this->returnValue(false));

        $this->executionContext->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo(self::ERROR_MESSAGE));
        $this->executionContext->expects($this->never())
            ->method('addViolationAt');

        $this->SUT->validate($entity, $constraint);
    }

    /**
     * @test
     */
    public function validateFailWithErrorPath()
    {
        $this->mockService();

        $constraint = new ServiceCallback(array(
            'service' => self::SERVICE_NAME,
            'method' =>  self::SERVICE_METHOD,
            'message' => self::ERROR_MESSAGE,
            'errorPath' => self::ERROR_PATH,
        ));

        $entity = new \stdClass();

        $this->service->expects($this->once())
            ->method(self::SERVICE_METHOD)
            ->with($entity)
            ->will($this->returnValue(false));

        $this->executionContext->expects($this->never())
            ->method('addViolation');
        $this->executionContext->expects($this->once())
            ->method('addViolationAt')
            ->with(
                $this->equalTo(self::ERROR_PATH),
                $this->equalTo(self::ERROR_MESSAGE)
            );

        $this->SUT->validate($entity, $constraint);
    }

    /**
     * @test
     */
    public function validateNull()
    {
        $constraint = new ServiceCallback(array(
            'service' => self::SERVICE_NAME,
            'method' =>  self::SERVICE_METHOD
        ));

        $return = $this->SUT->validate(null, $constraint);

        $this->assertThat($return, $this->equalTo(null));
    }

    /**
     * @test
     * @expectedException Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function nonexistentService()
    {
        $this->container->expects($this->once())
            ->method('has')
            ->with($this->equalTo('nonexistent.service'))
            ->will($this->returnValue(false));

        $constraint = new ServiceCallback(array(
            'service' => 'nonexistent.service',
            'method' =>  self::SERVICE_METHOD
        ));

        $entity = new \stdClass();

        $this->SUT->validate($entity, $constraint);
    }

    /**
     * @test
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @dataProvider illegalErrorPathData
     */
    public function illegalErrorPath($illegalErrorPath)
    {
        $this->container->expects($this->once())
            ->method('has')
            ->with($this->equalTo(self::SERVICE_NAME))
            ->will($this->returnValue(true));

        $constraint = new ServiceCallback(array(
            'service' => self::SERVICE_NAME,
            'method' =>  self::SERVICE_METHOD,
            'errorPath' => $illegalErrorPath
        ));

        $entity = new \stdClass();

        $this->SUT->validate($entity, $constraint);
    }

    public function illegalErrorPathData()
    {
        return array(
            'number' => array(1),
            'object' => array(new \stdClass()),
        );
    }

    /**
     * @test
     * @expectedException Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function nonexistentMethod()
    {
        $this->mockService();

        $constraint = new ServiceCallback(array(
            'service' => self::SERVICE_NAME,
            'method' =>  'nonexistentMethod',
        ));

        $entity = new \stdClass();

        $this->SUT->validate($entity, $constraint);
    }

    protected function setUp()
    {
        $this->SUT = new ServiceCallbackValidator();
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->SUT->setContainer($this->container);
        $this->service = $this->getMock('PHPMentors\ValidatorBundle\Tests\Fixtures\BarService');
        $this->container->set(self::SERVICE_NAME, $this->service);
        $this->executionContext = $this->getMock('Symfony\Component\Validator\ExecutionContextInterface');
        $this->SUT->initialize($this->executionContext);
    }

    private function mockService()
    {
        $this->container->expects($this->once())
            ->method('has')
            ->with($this->equalTo(self::SERVICE_NAME))
            ->will($this->returnValue(true));
        $this->container->expects($this->once())
            ->method('get')
            ->with($this->equalTo(self::SERVICE_NAME))
            ->will($this->returnValue($this->service));
    }
}