<?php
/**
 * Copyright 2014 Platinum Pixs, LLC. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

namespace PlatinumPixs\GoogleClosureLibrary\Tests\DependencyInjection;

use PlatinumPixs\GoogleClosureLibrary\DependencyInjection\PlatinumPixsGoogleClosureLibraryExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\Request;

class PlatinumPixsGoogleClosureLibraryExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->kernel = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');

        $this->container = new ContainerBuilder();
        $this->container->addScope(new Scope('request'));
        $this->container->register('request', 'Symfony\\Component\\HttpFoundation\\Request')->setScope('request');
        $this->container->register('templating.helper.assets', $this->getMockClass('Symfony\\Component\\Templating\\Helper\\AssetsHelper'));
        $this->container->register('templating.helper.router', $this->getMockClass('Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RouterHelper'))
                        ->addArgument(new Definition($this->getMockClass('Symfony\\Component\\Routing\\RouterInterface')));
        $this->container->register('twig', 'Twig_Environment');
        $this->container->setParameter('kernel.bundles', array());
        $this->container->setParameter('kernel.cache_dir', __DIR__);
        $this->container->setParameter('webroot', __DIR__);
        $this->container->setParameter('kernel.debug', false);
        $this->container->setParameter('kernel.root_dir', __DIR__);
        $this->container->setParameter('kernel.charset', 'UTF-8');
        $this->container->set('kernel', $this->kernel);
    }

    public function getDebugModes()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider getDebugModes
     */
    public function testBaseSetup($debug)
    {
        $this->container->setParameter('kernel.debug', $debug);
        $this->container->enterScope('request');
        $this->container->set('request', Request::create('/'));
        $this->container->set('kernel', $this->kernel);

        $extension = new PlatinumPixsGoogleClosureLibraryExtension();
        $extension->load(array(
            'platinum_pixs_google_closure_library' => array(
                'outputMode'        => 'compiled',
                'compilerFlags'     => array(
                    '--compilation_level=ADVANCED_OPTIMIZATIONS',
                    "--define='somevariableinside=somevalue'"
                ),
                'externs'           => array(
                    "src/PlatinumPixs/TestBundle/Resources/javascript/loggly-externs.js"
                ),
                'root'              => array(
                    "src/PlatinumPixs/TestBundle/Resources/javascript"
                )
            )
        ), $this->container);

        $errors = array();

        foreach ($this->container->getServiceIds() as $id) {
            try {
                $this->container->get($id);
            } catch (\Exception $e) {
                print($e->getMessage());
                $errors[$id] = $e->getMessage();
            }
        }

        self::assertEquals(array(), $errors, '');
    }
}
 