<?php

namespace Mesd\RuleBundle\Model\Builder;

use Mesd\RuleBundle\Model\Defintion\DefinitionManagerInterface;

interface DefinitionManagerAwareInterface
{
    /**
     * Returns a reference to the defintion manager
     * 
     * @return DefinitionManagerInterface The defintion manager
     */
    public function getDefinitionManager();
}