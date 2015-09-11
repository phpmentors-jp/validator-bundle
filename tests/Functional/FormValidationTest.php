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

namespace PHPMentors\ValidatorBundle\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @since Class available since Release 1.0.0
 */
class FormValidationTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $_SERVER['KERNEL_DIR'] = __DIR__.'/app';
        $this->removeCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->removeCacheDir();
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = array())
    {
        $kernel = KernelTestCase::createKernel($options);

        if (array_key_exists('config', $options)) {
            $kernel->setConfig($options['config']);
        }

        return $kernel;
    }

    private function removeCacheDir()
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($_SERVER['KERNEL_DIR'].'/cache/test');
    }

    /**
     * @return array
     */
    public function validateData()
    {
        return array(
            array('Atsuhiro', 'Kubo', 'confirmation'),
            array('Keigo', 'Kubo', 'input'),
            array('Kosuke', 'Kubo', 'input'),
        );
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $nextPage
     *
     * @test
     * @dataProvider validateData
     */
    public function validate($firstName, $lastName, $nextPage)
    {
        $client = static::createClient();
        $client->request('GET', '/user/registration/');

        $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
        $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains('input'));

        $form = $client->getCrawler()->selectButton('user_registration[next]')->form();
        $form['user_registration[firstName]'] = $firstName;
        $form['user_registration[lastName]'] = $lastName;

        $client->submit($form);

        $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(302));

        $client->request('GET', $client->getResponse()->headers->get('Location'));

        $this->assertThat($client->getResponse()->getStatusCode(), $this->equalTo(200));
        $this->assertThat($client->getCrawler()->filter('title')->text(), $this->stringContains($nextPage));
    }
}
