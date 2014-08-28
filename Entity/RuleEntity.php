<?php

namespace Mesd\RuleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RuleEntity
 */
class RuleEntity
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
     * @var \Mesd\RuleBundle\Entity\RulesetEntity
     */
    private $ruleset;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $thenRules;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $elseRules;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $thenActions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $elseActions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->thenRules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->elseRules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->thenActions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->elseActions = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return RuleEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set ruleset
     *
     * @param \Mesd\RuleBundle\Entity\RulesetEntity $ruleset
     * @return RuleEntity
     */
    public function setRuleset(\Mesd\RuleBundle\Entity\RulesetEntity $ruleset = null)
    {
        $this->ruleset = $ruleset;

        return $this;
    }

    /**
     * Get ruleset
     *
     * @return \Mesd\RuleBundle\Entity\RulesetEntity 
     */
    public function getRuleset()
    {
        return $this->ruleset;
    }

    /**
     * Add thenRules
     *
     * @param \Mesd\RuleBundle\Entity\RuleEntity $thenRules
     * @return RuleEntity
     */
    public function addThenRule(\Mesd\RuleBundle\Entity\RuleEntity $thenRules)
    {
        $this->thenRules[] = $thenRules;

        return $this;
    }

    /**
     * Remove thenRules
     *
     * @param \Mesd\RuleBundle\Entity\RuleEntity $thenRules
     */
    public function removeThenRule(\Mesd\RuleBundle\Entity\RuleEntity $thenRules)
    {
        $this->thenRules->removeElement($thenRules);
    }

    /**
     * Get thenRules
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThenRules()
    {
        return $this->thenRules;
    }

    /**
     * Add elseRules
     *
     * @param \Mesd\RuleBundle\Entity\RuleEntity $elseRules
     * @return RuleEntity
     */
    public function addElseRule(\Mesd\RuleBundle\Entity\RuleEntity $elseRules)
    {
        $this->elseRules[] = $elseRules;

        return $this;
    }

    /**
     * Remove elseRules
     *
     * @param \Mesd\RuleBundle\Entity\RuleEntity $elseRules
     */
    public function removeElseRule(\Mesd\RuleBundle\Entity\RuleEntity $elseRules)
    {
        $this->elseRules->removeElement($elseRules);
    }

    /**
     * Get elseRules
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getElseRules()
    {
        return $this->elseRules;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $conditionCollection;


    /**
     * Add conditionCollection
     *
     * @param \Mesd\RuleBundle\Entity\ConditionCollectionEntity $conditionCollection
     * @return RuleEntity
     */
    public function addConditionCollection(\Mesd\RuleBundle\Entity\ConditionCollectionEntity $conditionCollection)
    {
        $this->conditionCollection[0] = $conditionCollection;

        return $this;
    }

    /**
     * Remove conditionCollection
     *
     * @param \Mesd\RuleBundle\Entity\ConditionCollectionEntity $conditionCollection
     */
    public function removeConditionCollection(\Mesd\RuleBundle\Entity\ConditionCollectionEntity $conditionCollection)
    {
        $this->conditionCollection->removeElement($conditionCollection);
    }

    /**
     * Get conditionCollection
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConditionCollection()
    {
        if (count($this->conditionCollection) > 0) {
            return $this->conditionCollection[0];
        } else {
            return null;
        }
    }

    /**
     * Add thenActions
     *
     * @param \Mesd\RuleBundle\Entity\ActionCallEntity $thenActions
     * @return RuleEntity
     */
    public function addThenAction(\Mesd\RuleBundle\Entity\ActionCallEntity $thenActions)
    {
        $this->thenActions[] = $thenActions;

        return $this;
    }

    /**
     * Remove thenActions
     *
     * @param \Mesd\RuleBundle\Entity\ActionCallEntity $thenActions
     */
    public function removeThenAction(\Mesd\RuleBundle\Entity\ActionCallEntity $thenActions)
    {
        $this->thenActions->removeElement($thenActions);
    }

    /**
     * Get thenActions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThenActions()
    {
        return $this->thenActions;
    }

    /**
     * Add elseActions
     *
     * @param \Mesd\RuleBundle\Entity\ActionCallEntity $elseActions
     * @return RuleEntity
     */
    public function addElseAction(\Mesd\RuleBundle\Entity\ActionCallEntity $elseActions)
    {
        $this->elseActions[] = $elseActions;

        return $this;
    }

    /**
     * Remove elseActions
     *
     * @param \Mesd\RuleBundle\Entity\ActionCallEntity $elseActions
     */
    public function removeElseAction(\Mesd\RuleBundle\Entity\ActionCallEntity $elseActions)
    {
        $this->elseActions->removeElement($elseActions);
    }

    /**
     * Get elseActions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getElseActions()
    {
        return $this->elseActions;
    }
}
