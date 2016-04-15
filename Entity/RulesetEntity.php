<?php

namespace Mesd\RuleBundle\Entity;


/**
 * RulesetEntity.
 */
class RulesetEntity
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $context;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->context = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return RulesetEntity
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
     * Add context.
     *
     * @param \Mesd\RuleBundle\Entity\ContextEntity $context
     *
     * @return RulesetEntity
     */
    public function addContext(\Mesd\RuleBundle\Entity\ContextEntity $context)
    {
        $this->context[] = $context;

        return $this;
    }

    /**
     * Remove context.
     *
     * @param \Mesd\RuleBundle\Entity\ContextEntity $context
     */
    public function removeContext(\Mesd\RuleBundle\Entity\ContextEntity $context)
    {
        $this->context->removeElement($context);
    }

    /**
     * Get context.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContext()
    {
        return $this->context;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $rules;

    /**
     * Add rules.
     *
     * @param \Mesd\RuleBundle\Entity\RuleEntity $rules
     *
     * @return RulesetEntity
     */
    public function addRule(\Mesd\RuleBundle\Entity\RuleEntity $rules)
    {
        $this->rules[] = $rules;

        return $this;
    }

    /**
     * Remove rules.
     *
     * @param \Mesd\RuleBundle\Entity\RuleEntity $rules
     */
    public function removeRule(\Mesd\RuleBundle\Entity\RuleEntity $rules)
    {
        $this->rules->removeElement($rules);
    }

    /**
     * Get rules.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRules()
    {
        return $this->rules;
    }
}
