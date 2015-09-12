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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @since Class available since Release 1.2.0
 */
class RangeValidator extends \Symfony\Component\Validator\Constraints\RangeValidator implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConstraintDefinitionException
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Range) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Range');
        }

        if ($value instanceof \DateTime || $value instanceof \DateTimeInterface) {
            $newValue = clone $value;
            $newValue->setTime(0, 0, 0);
            $value = $newValue;
        }

        if (is_array($constraint->min)) {
            if (!(array_key_exists('service', $constraint->min) && array_key_exists('method', $constraint->min))) {
                throw new ConstraintDefinitionException(sprintf('The value of the option "min" as a factory expects the keys "service" and "method".'));
            }

            $minFactory = array($this->container->get($constraint->min['service']), $constraint->min['method']);
            if (!is_callable($minFactory)) {
                throw new ConstraintDefinitionException(sprintf('"%s::%s" targeted by Range constraint is not a valid callable.', get_class($minFactory[0]), $minFactory[1]));
            }

            $min = call_user_func($minFactory);
            if ($min instanceof \DateTime || $min instanceof \DateTimeInterface) {
                $constraint->min = clone $min;
                $constraint->min->modify('+1 seconds');
            } else {
                $constraint->min = $min;
            }
        }

        if (is_array($constraint->max)) {
            if (!(array_key_exists('service', $constraint->max) && array_key_exists('method', $constraint->max))) {
                throw new ConstraintDefinitionException(sprintf('The value of the option "max" as a factory expects the keys "service" and "method".'));
            }

            $maxFactory = array($this->container->get($constraint->max['service']), $constraint->max['method']);
            if (!is_callable($maxFactory)) {
                throw new ConstraintDefinitionException(sprintf('"%s::%s" targeted by Range constraint is not a valid callable.', get_class($maxFactory[0]), $maxFactory[1]));
            }

            $max = call_user_func($maxFactory);
            if ($max instanceof \DateTime || $max instanceof \DateTimeInterface) {
                $constraint->max = clone $max;
            } else {
                $constraint->max = $max;
            }
        }

        return parent::validate($value, $constraint);
    }
}
