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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ServiceCallbackValidator extends ConstraintValidator implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function validate($object, Constraint $constraint)
    {
        if (null === $object) {
            return;
        }

        if (!$this->container->has($constraint->service)) {
            throw new ConstraintDefinitionException();
        }

        if (null !== $constraint->errorPath && !is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        $service = $this->container->get($constraint->service);

        if (!method_exists($service, $constraint->method)) {
            throw new ConstraintDefinitionException(sprintf('Method "%s" targeted by ServiceCallback constraint does not exist', $constraint->method));
        }

        $result = call_user_func(array($service, $constraint->method), $object);

        if (false == $result) {
            if (null !== $constraint->errorPath) {
                $this->context->addViolationAt($constraint->errorPath, $constraint->message);
            } else {
                $this->context->addViolation($constraint->message);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
