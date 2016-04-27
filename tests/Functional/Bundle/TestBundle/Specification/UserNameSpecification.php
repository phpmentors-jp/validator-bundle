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

namespace PHPMentors\ValidatorBundle\Functional\Bundle\TestBundle\Specification;

use PHPMentors\DomainKata\Entity\EntityInterface;
use PHPMentors\DomainKata\Specification\SpecificationInterface;
use PHPMentors\ValidatorBundle\Functional\Bundle\TestBundle\Entity\User;

/**
 * @since Class available since Release 1.0.0
 */
class UserNameSpecification implements SpecificationInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy(EntityInterface $entity)
    {
        assert($entity instanceof User);

        return $entity->getFirstName() == 'Atsuhiro' && $entity->getLastName() == 'Kubo';
    }
}
