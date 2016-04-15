<?php

namespace Mesd\RuleBundle\Entity;


/**
 * ActionCallEntity.
 */
class ActionCallEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $raw_input_value;

    /**
     * @var \Mesd\RuleBundle\Entity\ActionEntity
     */
    private $action;

    /**
     * @var \Mesd\RuleBundle\Entity\RuleEntity
     */
    private $thenRule;

    /**
     * @var \Mesd\RuleBundle\Entity\RuleEntity
     */
    private $elseRule;

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set raw_input_value.
     *
     * @param string $rawInputValue
     *
     * @return ActionCallEntity
     */
    public function setRawInputValue($rawInputValue)
    {
        $this->raw_input_value = $rawInputValue;

        return $this;
    }

    /**
     * Get raw_input_value.
     *
     * @return string
     */
    public function getRawInputValue()
    {
        return $this->raw_input_value;
    }

    /**
     * Set action.
     *
     * @param \Mesd\RuleBundle\Entity\ActionEntity $action
     *
     * @return ActionCallEntity
     */
    public function setAction(\Mesd\RuleBundle\Entity\ActionEntity $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action.
     *
     * @return \Mesd\RuleBundle\Entity\ActionEntity
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set thenRule.
     *
     * @param \Mesd\RuleBundle\Entity\RuleEntity $thenRule
     *
     * @return ActionCallEntity
     */
    public function setThenRule(\Mesd\RuleBundle\Entity\RuleEntity $thenRule = null)
    {
        $this->thenRule = $thenRule;

        return $this;
    }

    /**
     * Get thenRule.
     *
     * @return \Mesd\RuleBundle\Entity\RuleEntity
     */
    public function getThenRule()
    {
        return $this->thenRule;
    }

    /**
     * Set elseRule.
     *
     * @param \Mesd\RuleBundle\Entity\RuleEntity $elseRule
     *
     * @return ActionCallEntity
     */
    public function setElseRule(\Mesd\RuleBundle\Entity\RuleEntity $elseRule = null)
    {
        $this->elseRule = $elseRule;

        return $this;
    }

    /**
     * Get elseRule.
     *
     * @return \Mesd\RuleBundle\Entity\RuleEntity
     */
    public function getElseRule()
    {
        return $this->elseRule;
    }
}
