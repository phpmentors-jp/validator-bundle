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

use PHPMentors\ValidatorBundle\Fixtures\AtLeastOneOfEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

/**
 * @since Class available since Release 1.1.0
 */
class AtLeastOneOfValidatorTest extends AbstractConstraintValidatorTest
{
    /**
     * {@inheritdoc}
     */
    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5_BC;
    }

    /**
     * {@inheritdoc}
     */
    protected function createValidator()
    {
        return new AtLeastOneOfValidator();
    }

    /**
     * @return array
     */
    public function validateData()
    {
        return array(
            array('foo', null, 0),
            array('foo', '', 0),
            array(null, 'bar', 0),
            array('', 'bar', 0),
            array(null, null, 2),
        );
    }

    /**
     * @param string $a
     * @param string $b
     * @param int    $violationCount
     *
     * @test
     * @dataProvider validateData
     */
    public function validate($a, $b, $violationCount)
    {
        $properties = array('a', 'b');
        $constraint = new AtLeastOneOf(array(
            'properties' => $properties,
        ));

        $value = new AtLeastOneOfEntity();
        $value->setA($a);
        $value->setB($b);

        $this->setObject($value);
        $this->metadata->addPropertyConstraint('a', new NotBlank());
        $this->metadata->addPropertyConstraint('b', new NotBlank());
        $this->metadata->addConstraint($constraint);
        $metadataFactory = $this->context->getMetadataFactory();
        $metadataFactory->method('getMetadataFor')->willReturn($this->metadata);

        $this->validator->validate($value, $constraint);

        $this->assertThat(count($this->context->getViolations()), $this->equalTo($violationCount));
    }

    /**
     * @test
     */
    public function setDefaultErrorPathsToProperties()
    {
        $properties = array('a', 'b');
        $constraint = new AtLeastOneOf(array(
            'properties' => $properties,
        ));

        $value = new AtLeastOneOfEntity();

        $this->setObject($value);
        $this->metadata->addPropertyConstraint('a', new NotBlank());
        $this->metadata->addPropertyConstraint('b', new NotBlank());
        $this->metadata->addConstraint($constraint);
        $metadataFactory = $this->context->getMetadataFactory();
        $metadataFactory->method('getMetadataFor')->willReturn($this->metadata);

        $this->validator->validate($value, $constraint);
        $violations = $this->context->getViolations();

        $this->assertThat(count($violations), $this->equalTo(2));

        foreach ($violations as $violation) { /* @var $violation ConstraintViolationInterface */
            $this->assertThat(in_array(substr($violation->getPropertyPath(), strrpos($violation->getPropertyPath(), '.') + 1), $properties), $this->isTrue());
        }
    }

    /**
     * @test
     */
    public function setSpecifiedErrorPathsToProperties()
    {
        $properties = array('a', 'b');
        $errorPaths = array('b', 'c');
        $constraint = new AtLeastOneOf(array(
            'properties' => $properties,
            'errorPaths' => $errorPaths,
        ));

        $value = new AtLeastOneOfEntity();

        $this->setObject($value);
        $this->metadata->addPropertyConstraint('a', new NotBlank());
        $this->metadata->addPropertyConstraint('b', new NotBlank());
        $this->metadata->addPropertyConstraint('c', new NotBlank());
        $this->metadata->addConstraint($constraint);
        $metadataFactory = $this->context->getMetadataFactory();
        $metadataFactory->method('getMetadataFor')->willReturn($this->metadata);

        $this->validator->validate($value, $constraint);
        $violations = $this->context->getViolations();

        $this->assertThat(count($violations), $this->equalTo(2));

        foreach ($violations as $violation) { /* @var $violation ConstraintViolationInterface */
            $this->assertThat(in_array(substr($violation->getPropertyPath(), strrpos($violation->getPropertyPath(), '.') + 1), $errorPaths), $this->isTrue());
        }
    }

    /**
     * @test
     */
    public function raiseExceptionWhenPropertyCountIsLessThanTwo()
    {
        $properties = array('a');
        $constraint = new AtLeastOneOf(array(
            'properties' => $properties,
        ));

        $value = new AtLeastOneOfEntity();

        $this->setObject($value);
        $this->metadata->addPropertyConstraint('a', new NotBlank());
        $this->metadata->addPropertyConstraint('b', new NotBlank());
        $this->metadata->addPropertyConstraint('c', new NotBlank());
        $this->metadata->addConstraint($constraint);
        $metadataFactory = $this->context->getMetadataFactory();
        $metadataFactory->method('getMetadataFor')->willReturn($this->metadata);

        try {
            $this->validator->validate($value, $constraint);
            $this->fail('An expected exception has not been raised.');
        } catch (ConstraintDefinitionException $e) {
        }
    }
}
