<?php

namespace Mesd\RuleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConditionEntity
 */
class ConditionEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $operator_value;

    /**
     * @var string
     */
    private $raw_input_value;

    /**
     * @var \Mesd\RuleBundle\Entity\AttributeEntity
     */
    private $attribute;

    /**
     * @var \Mesd\RuleBundle\Entity\ConditionCollectionEntity
     */
    private $collection;


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
     * Set operator_value
     *
     * @param string $operatorValue
     * @return ConditionEntity
     */
    public function setOperatorValue($operatorValue)
    {
        $this->operator_value = $operatorValue;

        return $this;
    }

    /**
     * Get operator_value
     *
     * @return string 
     */
    public function getOperatorValue()
    {
        return $this->operator_value;
    }

    /**
     * Set raw_input_value
     *
     * @param string $rawInputValue
     * @return ConditionEntity
     */
    public function setRawInputValue($rawInputValue)
    {
        $this->raw_input_value = $rawInputValue;

        return $this;
    }

    /**
     * Get raw_input_value
     *
     * @return string 
     */
    public function getRawInputValue()
    {
        return $this->raw_input_value;
    }

    /**
     * Set attribute
     *
     * @param \Mesd\RuleBundle\Entity\AttributeEntity $attribute
     * @return ConditionEntity
     */
    public function setAttribute(\Mesd\RuleBundle\Entity\AttributeEntity $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return \Mesd\RuleBundle\Entity\AttributeEntity 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set collection
     *
     * @param \Mesd\RuleBundle\Entity\ConditionCollectionEntity $collection
     * @return ConditionEntity
     */
    public function setCollection(\Mesd\RuleBundle\Entity\ConditionCollectionEntity $collection = null)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return \Mesd\RuleBundle\Entity\ConditionCollectionEntity 
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
