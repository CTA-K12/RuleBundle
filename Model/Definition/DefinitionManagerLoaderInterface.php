<?php

namespace Mesd\RuleBundle\Model\Definition;

use Mesd\RuleBundle\Model\Definition\DefinitionManagerInterface;

interface DefinitionManagerLoaderInterface
{
    /**
     * Load the defintion manager
     *
     * @return DefinitionManagerInterface The loaded definition manager
     */
    public function load();
}