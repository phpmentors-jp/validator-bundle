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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @package    PHPMentors_ValidatorBundle
 * @copyright  2014 GOTO Hidenori <hidenorigoto@gmail.com>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 * @since      File available since Release 0.1.0
 */
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
            throw new ConstraintDefinitionException;
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
