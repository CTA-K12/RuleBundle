<?php

namespace Mesd\RuleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConditionCollectionEntity
 */
class ConditionCollectionEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $chain_type;

    /**
     * @var \Mesd\RuleBundle\Entity\Rule
     */
    private $rule;

    /**
     * @var \Mesd\RuleBundle\Entity\ConditionCollectionEntity
     */
    private $parent;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set chain_type
     *
     * @param $chainType
     * @return ConditionCollectionEntity
     */
    public function setChainType($chainType)
    {
        $this->chain_type = $chainType;

        return $this;
    }

    /**
     * Get chain_type
     *
     * @return \255 
     */
    public function getChainType()
    {
        return $this->chain_type;
    }

    /**
     * Set rule
     *
     * @param \Mesd\RuleBundle\Entity\Rule $rule
     * @return ConditionCollectionEntity
     */
    public function setRule(\Mesd\RuleBundle\Entity\RuleEntity $rule = null)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get rule
     *
     * @return \Mesd\RuleBundle\Entity\Rule 
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set parent
     *
     * @param \Mesd\RuleBundle\Entity\ConditionCollectionEntity $parent
     * @return ConditionCollectionEntity
     */
    public function setParent(\Mesd\RuleBundle\Entity\ConditionCollectionEntity $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Mesd\RuleBundle\Entity\ConditionCollectionEntity 
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $subCollections;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $conditions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subCollections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->conditions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add subCollections
     *
     * @param \Mesd\RuleBundle\Entity\ConditionCollectionEntity $subCollections
     * @return ConditionCollectionEntity
     */
    public function addSubCollection(\Mesd\RuleBundle\Entity\ConditionCollectionEntity $subCollections)
    {
        $this->subCollections[] = $subCollections;

        return $this;
    }

    /**
     * Remove subCollections
     *
     * @param \Mesd\RuleBundle\Entity\ConditionCollectionEntity $subCollections
     */
    public function removeSubCollection(\Mesd\RuleBundle\Entity\ConditionCollectionEntity $subCollections)
    {
        $this->subCollections->removeElement($subCollections);
    }

    /**
     * Get subCollections
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubCollections()
    {
        return $this->subCollections;
    }

    /**
     * Add conditions
     *
     * @param \Mesd\RuleBundle\Entity\ConditionEntity $conditions
     * @return ConditionCollectionEntity
     */
    public function addCondition(\Mesd\RuleBundle\Entity\ConditionEntity $conditions)
    {
        $this->conditions[] = $conditions;

        return $this;
    }

    /**
     * Remove conditions
     *
     * @param \Mesd\RuleBundle\Entity\ConditionEntity $conditions
     */
    public function removeCondition(\Mesd\RuleBundle\Entity\ConditionEntity $conditions)
    {
        $this->conditions->removeElement($conditions);
    }

    /**
     * Get conditions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
