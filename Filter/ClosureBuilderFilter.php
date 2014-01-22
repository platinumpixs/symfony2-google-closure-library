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

namespace PlatinumPixs\GoogleClosureLibrary\Filter;

use \Assetic\Filter\FilterInterface,
    \Assetic\Asset\AssetInterface,
    \Symfony\Component\Process\ProcessBuilder,
    \PlatinumPixs\GoogleClosureLibrary\Exception\ProcessorError;

/**
 * Assetic {@link https://github.com/kriswallsmith/assetic} filter for the google closure builder file
 */
class ClosureBuilderFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $_webRoot;

    /**
     * @var string
     */
    private $_outputMode;

    /**
     * @var array
     */
    private $_compilerFlags;

    /**
     * @var array
     */
    private $_externs;

    /**
     * @var array
     */
    private $_root;

    /**
     * @param $webRoot
     * @param $outputMode
     * @param $compilerFlags
     * @param $externs
     * @param $root
     */
    public function __construct($webRoot, $outputMode, $compilerFlags, $externs, $root)
    {
        $this->_webRoot = $webRoot;
        $this->_outputMode = $outputMode;
        $this->_compilerFlags = $compilerFlags;
        $this->_externs = $externs;
        $this->_root = $root;
    }

    /**
     * @param $endPath
     * @return string
     */
    private function _buildPath($endPath)
    {
        return rtrim($this->_webRoot, '//') . DIRECTORY_SEPARATOR . ltrim($endPath, '//');
    }

    /**
     * @param \Assetic\Asset\AssetInterface $asset
     * @throws \PlatinumPixs\GoogleClosureLibrary\Exception\ProcessorError
     */
    public function filterLoad(AssetInterface $asset)
    {
        preg_match('/goog.provide\(\'(.*)\'\);/', $asset->getContent(), $matches);

        // the python file builds out the file - https://developers.google.com/closure/library/docs/closurebuilder
        $pb = new ProcessBuilder(array('python', __DIR__ . '/../closure-library/closure/bin/build/closurebuilder.py'));

        // automatically add the closure library directory for the javascript
        $pb->add('--root')->add(__DIR__ . '/../../../../google-closure-library/PlatinumPixs/GoogleClosureLibraryJavascript/');

        foreach ($this->_root as $root)
        {
            $pb->add('--root')->add($this->_buildPath($root));
        }

        $pb->add('--output_mode')->add($this->_outputMode);

        $pb->add('--namespace')->add($matches[1]);

        foreach ($this->_compilerFlags as $flag)
        {
            $pb->add('--compiler_flags')->add($flag);
        }

        foreach ($this->_externs as $extern)
        {
            $pb->add('--compiler_flags')->add(sprintf("--externs='%s'", $this->_buildPath($extern)));
        }

        $pb->add('--compiler_jar')->add(__DIR__ . '/../compiler.jar');

        $pb->add('--output_file')->add($cleanup[] = $output = tempnam(sys_get_temp_dir(), 'assetic_closurebuilder'));

        $proc = $pb->getProcess();

        $code = $proc->run();

        if (0 < $code) {
            throw new ProcessorError($proc->getErrorOutput());
        }

        $asset->setContent(file_get_contents($output));

        array_map('unlink', $cleanup);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
