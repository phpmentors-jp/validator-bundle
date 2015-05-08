<?php
/*
 * Copyright (c) 2014 GOTO Hidenori <hidenorigoto@gmail.com>,
 * All rights reserved.
 *
 * This file is part of PHPMentorsValidatorBundle.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace PHPMentors\ValidatorBundle\Constraints;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ExecutionContextInterface;

class ServiceCallbackValidatorTest extends \PHPUnit_Framework_TestCase
{
    const SERVICE_NAME = 'test.bar_service';
    const SERVICE_METHOD = 'bazMethod';
    const ERROR_MESSAGE = 'error message';
    const ERROR_PATH = 'someProperty';

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
            'method' => self::SERVICE_METHOD,
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
            'method' => self::SERVICE_METHOD,
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
            'method' => self::SERVICE_METHOD,
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
            'method' => self::SERVICE_METHOD,
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
            'method' => self::SERVICE_METHOD,
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
            'method' => self::SERVICE_METHOD,
            'errorPath' => $illegalErrorPath,
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
            'method' => 'nonexistentMethod',
        ));

        $entity = new \stdClass();

        $this->SUT->validate($entity, $constraint);
    }

    protected function setUp()
    {
        $this->SUT = new ServiceCallbackValidator();
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->SUT->setContainer($this->container);
        $this->service = $this->getMock('PHPMentors\ValidatorBundle\Fixtures\BarService');
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
