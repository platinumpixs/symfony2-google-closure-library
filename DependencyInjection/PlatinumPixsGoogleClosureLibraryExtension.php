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

        if (isset($configs[1]['closureCompiler']['formatting']))
        {
            $configs[0]['closureCompiler']['compilerFlags'][] = sprintf("--formatting=%s", $configs[1]['closureCompiler']['formatting']);
        }
        elseif (isset($configs[0]['closureCompiler']['formatting']))
        {
            $configs[0]['closureCompiler']['compilerFlags'][] = sprintf("--formatting=%s", $configs[0]['closureCompiler']['formatting']);
        }

        if ((isset($configs[1]['closureCompiler']['debug']) && $configs[1]['closureCompiler']['debug'] === TRUE) ||
            (isset($configs[0]['closureCompiler']['debug']) && $configs[0]['closureCompiler']['debug'] === TRUE))
        {
            //$configs[0]['closureCompiler']['compilerFlags'][] = sprintf("--debug=%s", 'true');
            $configs[0]['closureCompiler']['compilerFlags'][] = sprintf("--define='goog.DEBUG=%s'", 'true');
        }
        else
        {
            $configs[0]['closureCompiler']['compilerFlags'][] = sprintf("--debug=%s", 'false');
            $configs[0]['closureCompiler']['compilerFlags'][] = sprintf("--define='goog.DEBUG=%s'", 'false');
        }

        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('platinum_pixs_assetic.closureCompiler.outputMode', $config['closureCompiler']['outputMode']);
        $container->setParameter('platinum_pixs_assetic.closureCompiler.compilerFlags', $config['closureCompiler']['compilerFlags']);
        $container->setParameter('platinum_pixs_assetic.closureCompiler.externs', $config['closureCompiler']['externs']);
        $container->setParameter('platinum_pixs_assetic.closureCompiler.root', $config['closureCompiler']['root']);
    }

}
