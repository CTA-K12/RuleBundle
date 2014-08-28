<?php

namespace Mesd\RuleBundle\Model\Context;

use Mesd\RuleBundle\Model\Context\ContextCollectionInterface;

interface ContextCollectionAwareInterface
{
    /**
     * Get the context collection currently associated with the ruleset
     *
     * @return ContextCollectionInterface The context collection
     */
    public function getContextCollection();
}