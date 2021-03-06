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

namespace PHPMentors\ValidatorBundle\Functional\Bundle\TestBundle\Controller;

use PHPMentors\PageflowerBundle\Annotation\Accept;
use PHPMentors\PageflowerBundle\Annotation\EndPage;
use PHPMentors\PageflowerBundle\Annotation\Init;
use PHPMentors\PageflowerBundle\Annotation\Page;
use PHPMentors\PageflowerBundle\Annotation\Pageflow;
use PHPMentors\PageflowerBundle\Annotation\StartPage;
use PHPMentors\PageflowerBundle\Annotation\Stateful;
use PHPMentors\PageflowerBundle\Annotation\Transition;
use PHPMentors\PageflowerBundle\Controller\ConversationalControllerInterface;
use PHPMentors\PageflowerBundle\Conversation\ConversationContext;
use PHPMentors\ValidatorBundle\Functional\Bundle\TestBundle\Entity\User;
use PHPMentors\ValidatorBundle\Functional\Bundle\TestBundle\Form\Type\UserRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @since Class available since Release 1.0.0
 *
 * @Pageflow({
 *     @StartPage({"input",
 *         @Transition("confirmation"),
 *     }),
 *     @Page({"confirmation",
 *         @Transition("success"),
 *         @Transition("input")
 *     }),
 *     @EndPage("success")
 * })
 */
class UserRegistrationController extends Controller implements ConversationalControllerInterface
{
    const VIEW_INPUT = 'TestBundle:UserRegistration:input.html.twig';
    const VIEW_CONFIRMATION = 'TestBundle:UserRegistration:confirmation.html.twig';
    const VIEW_SUCCESS = 'TestBundle:UserRegistration:success.html.twig';

    /**
     * @var ConversationContext
     */
    private $conversationContext;

    /**
     * @var User
     *
     * @Stateful
     */
    private $user;

    /**
     * {@inheritdoc}
     */
    public function setConversationContext(ConversationContext $conversationContext)
    {
        $this->conversationContext = $conversationContext;
    }

    /**
     * @Init
     */
    public function initialize()
    {
        $this->user = new User();
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Accept("input")
     * @Accept("confirmation")
     */
    public function inputGetAction(Request $request)
    {
        if ($this->conversationContext->getCurrentPage()->getPageId() == 'confirmation') {
            $this->conversationContext->getConversation()->transition('input');
        }

        $form = $this->createForm('PHPMentors\ValidatorBundle\Functional\Bundle\TestBundle\Form\Type\UserRegistrationType', $this->user, array(
            'action' => $this->generateUrl('test_user_registration_input_post'),
            'method' => 'POST',
        ));

        return $this->render(self::VIEW_INPUT, array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     *
     * @Accept("input")
     */
    public function inputPostAction(Request $request)
    {
        $form = $this->createForm('PHPMentors\ValidatorBundle\Functional\Bundle\TestBundle\Form\Type\UserRegistrationType', $this->user, array(
            'action' => $this->generateUrl('test_user_registration_input_post'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->conversationContext->getConversation()->transition('confirmation');

            return $this->redirect($this->conversationContext->generateUrl('test_user_registration_confirmation_get'));
        } else {
            return $this->redirect($this->conversationContext->generateUrl('test_user_registration_input_get'));
        }
    }

    /**
     * @param Request $request
     *
     * @Accept("confirmation")
     */
    public function confirmationGetAction(Request $request)
    {
        $form = $this->createFormBuilder(null, array('action' => $this->generateUrl('test_user_registration_confirmation_post'), 'method' => 'POST'))
            ->add('next', 'Symfony\Component\Form\Extension\Core\Type\SubmitType')
            ->add('prev', 'Symfony\Component\Form\Extension\Core\Type\SubmitType')
            ->getForm();

        return $this->render(self::VIEW_CONFIRMATION, array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     *
     * @Accept("confirmation")
     */
    public function confirmationPostAction(Request $request)
    {
        $form = $this->createFormBuilder(null, array('action' => $this->generateUrl('test_user_registration_confirmation_post'), 'method' => 'POST'))
            ->add('next', 'Symfony\Component\Form\Extension\Core\Type\SubmitType')
            ->add('prev', 'Symfony\Component\Form\Extension\Core\Type\SubmitType')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($form->get('next')->isClicked()) {
                $this->conversationContext->getConversation()->transition('success');

                return $this->redirect($this->conversationContext->generateUrl('test_user_registration_success_get'));
            } elseif ($form->get('prev')->isClicked()) {
                $this->conversationContext->getConversation()->transition('input');

                return $this->redirect($this->conversationContext->generateUrl('test_user_registration_input_get'));
            }
        } else {
            return $this->redirect($this->conversationContext->generateUrl('test_user_registration_confirmation_get'));
        }
    }

    /**
     * @param Request $request
     *
     * @Accept("success")
     */
    public function successGetAction(Request $request)
    {
        return $this->render(self::VIEW_SUCCESS);
    }
}
