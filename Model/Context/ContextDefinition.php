<?php

namespace Mesd\RuleBundle\Model\Context;

class ContextDefinition
{
    ///////////////
    // CONSTANTS //
    ///////////////

    //Types
    const TYPE_PRIMATIVE = 'primative';
    const TYPE_OBJECT = 'object';
    const TYPE_INTERFACE = 'interface';

    //Primatives
    const PRIMATIVE_INT = 'int';
    const PRIMATIVE_STRING = 'string';
    const PRIMATIVE_FLOAT = 'float';
    const PRIMATIVE_BOOLEAN = 'bool';

    //Exceptions
    const ERROR_NOT_VALID_TYPE = 'The given type is not recognized by this class';
    const ERROR_NOT_VALID_PRIMATIVE = 'The given name is not valid for a primative type';

    ///////////////
    // VARIABLES //
    ///////////////

    /**
     * The name of the definition (e.g. Class name, Superclass name, interface, string, etc)
     * @var string
     */
    private $name;

    /**
     * The type of the definition (e.g. primative, class, interface)
     * @var string
     */
    private $type;

    ////////////////////
    // STATIC METHODS //
    ////////////////////


    /**
     * Gets a list of the types recognized by this class
     *
     * @return array String names of the recognized types
     */
    public static function getValidTypes() {
        return array(
            self::TYPE_PRIMATIVE,
            self::TYPE_OBJECT,
            self::TYPE_INTERFACE
        );
    }


    /**
     * Gets a list of the primative names recognized by this class
     *
     * @return array String names of the recognized primative names
     */
    public static function getValidPrimativeNames() {
        return array(
            self::PRIMATIVE_INT,
            self::PRIMATIVE_FLOAT,
            self::PRIMATIVE_BOOLEAN,
            self::PRIMATIVE_STRING
        );
    }


    /**
     * Checks whether a given type is recognized by this class or not
     *
     * @param  string  $type The type to check
     *
     * @return boolean       Whether the type is recognized or not
     */
    public static function isValidType($type) {
        return in_array($type, self::getValidTypes());
    }


    /**
     * Checks whether a given name is a recognized by this class for a primative type
     *
     * @param    $name [description]
     *
     * @return boolean       [description]
     */
    public static function isValidPrimativeName($name) {
        return in_array($name, self::getValidPrimativeNames());
    }


    //////////////////
    // BASE METHODS //
    //////////////////


    /**
     * Constructor
     *
     * @param string $name The name of the classification
     * @param string $type The type of the classification
     */
    public function __construct($name, $type) {
        //Check that the name and type are valid
        if (!self::isValidType($type)) {
            throw new \Exception(self::ERROR_NOT_VALID_TYPE);
        }
        if (self::TYPE_PRIMATIVE === $type) {
            if (!self::isValidPrimativeName($name)) {
                throw new \Exception(self::ERROR_NOT_VALID_PRIMATIVE);
            }
        }

        //set stuff
        $this->name = $name;
        $this->type = $type;
    }


    /////////////
    // METHODS //
    /////////////


     /**
     * Checks whether a given object matches the context definition
     *
     * @param  mixed   $context The object/type to check
     *
     * @return boolean          Whether the object matches the context definition
     */
    public function matches($context) {
        //Figure out which way to check the context
        if (self::TYPE_INTERFACE === $this->classificationType) {
            return ($context instanceof $this->classificationName);
        } elseif (self::TYPE_OBJECT === $this->classificationType) {
            return ($context instanceof $this->classificationName);
        } elseif (self::TYPE_PRIMATIVE === $this->classificationType) {
            return $this->matchesPrimative($context);
        }
        return false;
    }


    /**
     * Checks whether the variable is a particular type
     *
     * @param  mixed   $context The variable to check
     *
     * @return boolean          Whether it is particular type
     */
    protected function matchesPrimative($context) {
        //Determine the name of the classification and check that the context matches
        if (self::PRIMATIVE_INT === $this->classificationName) {
            return is_int($context);
        } elseif (self::PRIMATIVE_STRING === $this->classificationName) {
            return is_string($context);
        } elseif (self::PRIMATIVE_BOOLEAN === $this->classificationName) {
            return is_bool($context);
        } elseif (self::PRIMATIVE_FLOAT === $this->classificationName) {
            return is_float($context);
        }
        return false;
    }


    /////////////
    // GETTERS //
    /////////////

    /**
     * Gets the The name of the definition (e.g. Class name, Superclass name, interface, string, etc).
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Gets the The type of the definition (e.g. primative, class, interface).
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    }