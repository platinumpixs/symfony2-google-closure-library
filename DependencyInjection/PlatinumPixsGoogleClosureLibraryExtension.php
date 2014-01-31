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

namespace PlatinumPixs\GoogleClosureLibrary\DependencyInjection;

use \Symfony\Component\HttpKernel\DependencyInjection\Extension,
    \Symfony\Component\DependencyInjection\ContainerBuilder,
    \Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    \Symfony\Component\Config\FileLocator,
    \Symfony\Component\Config\Definition\Processor;

/**
 * Holds the dependency injection setup for the assetic information
 */
class PlatinumPixsGoogleClosureLibraryExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');

        $processor = new Processor();
        $configuration = new Configuration();

        // merges the configs 1 array key into 0 array key - 1 is from the dev settings
        if (isset($configs[1]))
        {
            $configs = array(0 => array_merge($configs[0], $configs[1]));
        }

        if (isset($configs[0]['formatting']))
        {
            $configs[0]['compilerFlags'][] = sprintf("--formatting=%s", $configs[0]['formatting']);
            unset($configs[0]['formatting']);
        }

        if (isset($configs[0]['debug']))
        {
            $configs[0]['compilerFlags'][] = sprintf("--define='goog.DEBUG=%s'", $configs[0]['debug'] === TRUE ? 'true' : 'false');

            unset($configs[0]['debug']);
        }
        else
        {
            $configs[0]['compilerFlags'][] = sprintf("--define='goog.DEBUG=%s'", 'false');
        }

        if (isset($configs[0]['debugOutput']))
        {
            $configs[0]['compilerFlags'][] = sprintf("--debug=%s", $configs[0]['debugOutput'] === TRUE ? 'true' : 'false');
            unset($configs[0]['debugOutput']);
        }
        else
        {
            $configs[0]['compilerFlags'][] = sprintf("--debug=%s", 'false');
        }

        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('platinum_pixs_google_closure_library.outputMode', $config['outputMode']);
        $container->setParameter('platinum_pixs_google_closure_library.compilerFlags', $config['compilerFlags']);
        $container->setParameter('platinum_pixs_google_closure_library.externs', $config['externs']);
        $container->setParameter('platinum_pixs_google_closure_library.root', $config['root']);
    }

}
