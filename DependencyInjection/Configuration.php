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

use \Symfony\Component\Config\Definition\Builder\TreeBuilder,
    \Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Builds out the tree for the config options in the yaml file
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('platinum_pixs_google_closure_library');

        return $rootNode->children()
                    ->scalarNode('outputMode', 'scalar')
                        ->isRequired()
                        ->end()
                    ->arrayNode('compilerFlags', 'array')
                        ->isRequired()
                        ->prototype('scalar')->end()
                        ->end()
                    ->arrayNode('root', 'array')
                        ->isRequired()
                        ->prototype('scalar')->end()
                        ->end()
                    ->arrayNode('externs', 'array')
                        ->prototype('scalar')->end()
                        ->end()
                ->end()
            ->end();
    }
}