<?php

namespace Mesd\RuleBundle\Entity;


/**
 * ActionEntity.
 */
class ActionEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class;

    /**
     * @var \Mesd\RuleBundle\Entity\ContextEntity
     */
    private $context;

    /**
     * @var \Mesd\RuleBundle\Entity\ServiceEntity
     */
    private $service;

    /**
     * @var \Mesd\RuleBundle\Entity\InputEntity
     */
    private $input;

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
     * Set name.
     *
     * @param string $name
     *
     * @return ActionEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set class.
     *
     * @param string $class
     *
     * @return ActionEntity
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set context.
     *
     * @param \Mesd\RuleBundle\Entity\ContextEntity $context
     *
     * @return ActionEntity
     */
    public function setContext(\Mesd\RuleBundle\Entity\ContextEntity $context = null)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context.
     *
     * @return \Mesd\RuleBundle\Entity\ContextEntity
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set service.
     *
     * @param \Mesd\RuleBundle\Entity\ServiceEntity $service
     *
     * @return ActionEntity
     */
    public function setService(\Mesd\RuleBundle\Entity\ServiceEntity $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service.
     *
     * @return \Mesd\RuleBundle\Entity\ServiceEntity
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set input.
     *
     * @param \Mesd\RuleBundle\Entity\InputEntity $input
     *
     * @return ActionEntity
     */
    public function setInput(\Mesd\RuleBundle\Entity\InputEntity $input = null)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Get input.
     *
     * @return \Mesd\RuleBundle\Entity\InputEntity
     */
    public function getInput()
    {
        return $this->input;
    }
}
