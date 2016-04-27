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

use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

/**
 * @since Class available since Release 1.2.0
 */
class RangeValidatorTest extends AbstractConstraintValidatorTest
{
    /**
     * @var int
     */
    private $max;

    /**
     * @var int
     */
    private $min;

    /**
     * {@inheritdoc}
     */
    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    /**
     * {@inheritdoc}
     */
    protected function createValidator()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->method('get')
            ->with('phpmentors_validator.range_validator_test')
            ->will($this->returnValue($this));
        $validator = new RangeValidator();
        $validator->setContainer($container);

        return $validator;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return array
     */
    public function validateData()
    {
        return array(
            array(null, null, 39, 0),
            array(20, null, 19, 1),
            array(20, null, 20, 0),
            array(20, null, 21, 0),
            array(null, 40, 39, 0),
            array(null, 40, 40, 0),
            array(null, 40, 41, 1),
            array(20, 40, 19, 1),
            array(20, 40, 20, 0),
            array(20, 40, 21, 0),
            array(20, 40, 39, 0),
            array(20, 40, 40, 0),
            array(20, 40, 41, 1),
            array($this->date('1974-09-13'), $this->date('1995-09-12'), $this->date('1995-09-11'), 0),
            array($this->date('1974-09-13'), $this->date('1995-09-12'), $this->date('1995-09-12'), 0),
            array($this->date('1974-09-13'), $this->date('1995-09-12'), $this->date('1995-09-13'), 1),
            array($this->date('1974-09-13'), $this->date('1995-09-12'), $this->date('1974-09-11'), 1),
            array($this->date('1974-09-13'), $this->date('1995-09-12'), $this->date('1974-09-12'), 1),
            array($this->date('1974-09-13'), $this->date('1995-09-12'), $this->date('1974-09-13'), 0),
        );
    }

    /**
     * @param string $target
     *
     * @return \DateTime
     */
    private function date($target)
    {
        return new \DateTime($target, new \DateTimeZone('UTC'));
    }

    /**
     * @test
     * @dataProvider validateData
     */
    public function validate($min, $max, $target, $violationCount)
    {
        $this->min = $min;
        $this->max = $max;

        $constraint = new Range(array(
            'min' => array(
                'service' => 'phpmentors_validator.range_validator_test',
                'method' => 'getMin',
            ),
            'max' => array(
                'service' => 'phpmentors_validator.range_validator_test',
                'method' => 'getMax',
            ),
        ));

        $this->validator->validate($target, $constraint);
        $violations = $this->context->getViolations();

        $this->assertThat(count($violations), $this->equalTo($violationCount));
    }

    /**
     * @return array
     */
    public function raiseExceptionWhenFactoryIsInvalidData()
    {
        return array(
            array(array('min' => array())),
            array(array('min' => array('service' => 'foo'))),
            array(array('min' => array('method' => 'bar'))),
            array(array('max' => array())),
            array(array('max' => array('service' => 'foo'))),
            array(array('max' => array('method' => 'bar'))),
        );
    }

    /**
     * @test
     * @dataProvider raiseExceptionWhenFactoryIsInvalidData
     */
    public function raiseExceptionWhenFactoryIsInvalid($options)
    {
        $constraint = new Range($options);

        try {
            $this->validator->validate(39, $constraint);
            $this->fail('An expected exception has not been raised.');
        } catch (ConstraintDefinitionException $e) {
        }
    }

    /**
     * @return array
     */
    public function raiseExceptionWhenFactoryIsInvalidCallbackData()
    {
        return array(
            array('min'),
            array('max'),
        );
    }

    /**
     * @test
     * @dataProvider raiseExceptionWhenFactoryIsInvalidCallbackData
     */
    public function raiseExceptionWhenFactoryIsInvalidCallback($option)
    {
        $constraint = new Range(array(
            $option => array(
                'service' => 'phpmentors_validator.range_validator_test',
                'method' => 'nonExistingMethod',
            ),
        ));

        try {
            $this->validator->validate(39, $constraint);
            $this->fail('An expected exception has not been raised.');
        } catch (ConstraintDefinitionException $e) {
        }
    }
}
