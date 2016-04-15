<?php

namespace Mesd\RuleBundle\Model\Form\Helper;

use Mesd\RuleBundle\Model\Comparator\ComparatorInterface;
use Symfony\Component\Form\FormBuilderInterface;

class ComparatorFormHelper
{
    ////////////////////
    // STATIC METHODS //
    ////////////////////


    /**
     * Adds a comparator selector to a given form builder.
     *
     * @param FormBuilderInterface $builder    The form builder to add the comparator selector to
     * @param ComparatorInterface  $comparator The comparator to convert to a select field
     *
     * @return FormBuilderInterface The form builder
     */
    public static function buildForm(FormBuilderInterface $builder, ComparatorInterface $comparator)
    {
        //Convert the operators to array of selector choices
        $selectorChoices = [];
        foreach ($comparator->getOperators() as $operator) {
            $selectorChoices[$operator->getValue()] = $operator->getName();
        }

        //Add the selector field
        $builder->add('comparator', 'choice', [
                'required' => 'true',
                'choices'  => $selectorChoices,
            ]
        );

        return $builder;
    }
}
