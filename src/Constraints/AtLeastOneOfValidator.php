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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AtLeastOneOfValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof AtLeastOneOf)) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\AtLeastOneOf');
        }

        if (!is_array($constraint->properties)) {
            throw new UnexpectedTypeException($constraint->properties, 'array');
        }

        $properties = (array) $constraint->properties;
        if (count($properties) < 2) {
            throw new ConstraintDefinitionException('At least two properties have to be specified.');
        }

        if ($constraint->errorPaths !== null && !is_array($constraint->errorPaths)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'array or null');
        }

        $metadata = $this->context->getValidator()->getMetadataFor($value);
        if ($metadata === null) {
            throw new InvalidArgumentException(sprintf('The metadata is not found for the class "%s".', get_class($value)));
        }

        foreach ($constraint->properties as $property) {
            if (!$metadata->hasPropertyMetadata($property)) {
                continue;
            }

            foreach ($metadata->getPropertyMetadata($property) as $propertyMetadata) {
                $propertyValue = $propertyMetadata->getPropertyValue($value);
                if ($propertyValue !== null && $propertyValue !== '') {
                    return;
                }
            }
        }

        $errorPaths = $constraint->errorPaths === null ? $constraint->properties : $constraint->errorPaths;
        foreach ($errorPaths as $errorPath) {
            $this->context->buildViolation($constraint->message)
                ->atPath($errorPath)
                ->setParameter('properties', implode(', ', $constraint->properties))
                ->addViolation()
                ;
        }
    }
}
