<?php

namespace Mesd\RuleBundle\Model\Definition;


interface DefinitionManagerLoaderInterface
{
    /**
     * Load the defintion manager.
     *
     * @return DefinitionManagerInterface The loaded definition manager
     */
    public function load();
}
