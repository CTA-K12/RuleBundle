<?php

namespace Mesd\RuleBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Mesd\RuleBundle\Model\Form\Helper\ComparatorFormHelper;

class FormController extends ContainerAware
{
    ////////////////////////
    // RENDERED RESPONSES //
    ////////////////////////


    /**
     * List the links to the ruleset forms
     *
     * @return Response The list of ruleset forms
     */
    public function listRulesetsAction()
    {
        //Get a list of all the ruleset names
        $rulesets = $this->container->get('mesd_rule.rules')->getRulesetNames();

        //Render
        return new Response($this->container->get('templating')->render('MesdRuleBundle:Form:list.html.twig', array(
            'rulesets' => $rulesets
        )));
    }


    /**
     * Displays the form
     *
     * @param  string   $rulesetName The url encoded name of the ruleset to display
     *
     * @return Response              The form
     */
    public function displayFormAction($encodedRulesetName)
    {
        //Decode the ruleset name
        $rulesetName = urldecode($encodedRulesetName);

        //Render the form page
        return new Response($this->container->get('templating')->render('MesdRuleBundle:Form:ruleform.html.twig', array(
            'rulesetName' => $rulesetName
        )));
    }


    ///////////////
    // PARTIALS  //
    ///////////////


    /**
     * Render the attribute list prototype
     *
     * @param  string   $encodedRulesetName The url encoded ruleset name
     *
     * @return Response                     The rendered list twig
     */
    public function renderAttributeListAction($encodedRulesetName)
    {
        //Decode the ruleset name
        $rulesetName = urldecode($encodedRulesetName);

        //Create the attribute array
        $attributes = array('contexts' => array(), 'services' => array());

        //Load all of the possible attributes
        $rsDef = $this->container->get('mesd_rule.rules')->getRulesetDefinition($rulesetName);
        foreach($rsDef->getContextCollection()->getContexts() as $context) {
            $attributes['contexts'][$context->getName()] = array();
            foreach($this->container->get('mesd_rule.rules')->getDefinitionManager()
                ->getAllContextAttributes($context->getName()) as $attr) {
                $attributes['contexts'][$context->getName()][] = $attr;
            }
        }
        foreach($this->container->get('mesd_rule.rules')->getDefinitionManager()->getAllServiceAttributes() as $attr) {
            $attributes['services'][] = $attr;
        }

        //sort the arrays
        ksort($attributes['contexts']);
        usort($attributes['services'], function($a, $b) {
            return strcasecmp($a->getName(), $b->getName());
        });
        foreach($attributes['contexts'] as $context => $attrs) {
            usort($attributes['contexts'][$context], function($a, $b) {
                return strcasecmp($a->getName(), $b->getName());
            });
        }

        //render and return the twig
        return new Response($this->container->get('templating')->render('MesdRuleBundle:Form:attributeList.html.twig', array(
            'attributes' => $attributes
        )));
    }


    /**
     * Render the action list prototype
     *
     * @param  string   $encodedRulesetName The url encoded ruleset name
     *
     * @return Response                     The rendered list twig
     */
    public function renderActionListAction($encodedRulesetName)
    {
        //Decode the ruleset name
        $rulesetName = urldecode($encodedRulesetName);

        //Create the actions array
        $actions = array('contexts' => array(), 'services' => array());

        //Load all of the possible actions
        $rsDef = $this->container->get('mesd_rule.rules')->getRulesetDefinition($rulesetName);
        foreach($rsDef->getContextCollection()->getContexts() as $context) {
            $actions['contexts'][$context->getName()] = array();
            foreach($this->container->get('mesd_rule.rules')->getDefinitionManager()
                ->getAllContextActions($context->getName()) as $action) {
                $actions['contexts'][$context->getName()][] = $action;
            }
        }
        foreach($this->container->get('mesd_rule.rules')->getDefinitionManager()->getAllServiceActions() as $action) {
            $actions['services'][] = $action;
        }

        //render and return the twig
        return new Response($this->container->get('templating')->render('MesdRuleBundle:Form:actionList.html.twig', array(
            'actions' => $actions
        )));
    }


    /**
     * Render the comparator form and the input form
     *
     * @param  string   $encodedAttributeName The name of the attribute
     * @param  string   $encodedContextName   The name of the context or null if is service attribute
     *
     * @return Response                       The rendered attribute input
     */
    public function renderComparatorAndInputAction($encodedAttributeName, $encodedContextName = null)
    {
        //Decode
        $attributeName = urldecode($encodedAttributeName);
        if (null !== $encodedContextName) {
            $contextName = urldecode($encodedContextName);
        } else {
            $contextName = null;
        }

        //Get the attribute
        if (null === $contextName) {
            //Get service attribtue
            $attr = $this->container->get('mesd_rule.rules')
                ->getDefinitionManager()->getServiceAttribute($attributeName);
        } else {
            //Get context attribute
            $attr = $this->container->get('mesd_rule.rules')
                ->getDefinitionManager()->getContextAttribute($contextName, $attributeName);
        }

        //Build the form pieces for the comparator and the input
        $builder = $this->container->get('form.factory')->createBuilder();
        ComparatorFormHelper::buildForm($builder, $attr->getComparator());
        $builder->add('input', $attr->getInput()->getFormType(), $attr->getInput()->getFormOptions());
        $form = $builder->getForm();

        //Render and return the partial twig
        return new Response($this->container->get('templating')->render('MesdRuleBundle:Form:attributeInput.html.twig', array(
            'form' => $form->createView()
        )));
    }


    /**
     * Render the input for a given action
     *
     * @param  string   $encodedActionName  The url encoded name of the action
     * @param  string   $encodedContextName The url encoded name of the context if exists
     *
     * @return Response                     The rendered input for the action
     */
    public function renderActionInputAction($encodedActionName, $encodedContextName = null)
    {
        //Decode
        $actionName = urldecode($encodedActionName);
        if (null !== $encodedContextName) {
            $contextName = urldecode($encodedContextName);
        } else {
            $contextName = null;
        }

        //Get the action
        if (null === $contextName) {
            //Get the service action
            $action = $this->container->get('mesd_rule.rules')
                ->getDefinitionManager()->getServiceAction($actionName);
        } else {
            //Get the context action
            $action = $this->container->get('mesd_rule.rules')
                ->getDefinitionManager()->getContextAction($contextName, $actionName);
        }

        //Build the form piece for the input
        $builder = $this->container->get('form.factory')->createBuilder();
        $builder->add('input', $action->getInput()->getFormType(), $action->getInput()->getFormOptions());
        $form = $builder->getForm();

        //Render and return the partial twig
        return new Response($this->container->get('templating')->render('MesdRuleBundle:Form:actionInput.html.twig', array(
            'form' => $form->createView()
        )));
    }


    ////////////////////
    // JSON RESPONSES //
    ////////////////////


    /**
     * Saves the json ruleset
     *
     * @param  Request      $request The request
     *
     * @return JsonResponse          The json response detailing the success of the operation or the errors
     */
    public function saveFormAction(Request $request)
    {
        //Create the return array
        $return = array('success' => true);

        //Get the data from the request
        $rulesetData = $request->request->get('ruleset');

        //Validate it
        $errors = $this->container->get('mesd_rule.rules')->getStorageManager()->validateArray($rulesetData);
        if (0 < count($errors)) {
            $return['success'] = false;
            $return['errors'] = $errors;
            return new JsonResponse($return);
        }

        //Convert to a ruleset object
        $ruleset = $this->container->get('mesd_rule.rules')->getStorageManager()->buildFromArray($rulesetData);

        //Check that there are root rules and no cycles
        if (!$ruleset->checkThatRootRulesExist()) {
            $return['success'] = false;
            $return['errors']['global'] = 
                'There are no rules that serve as start rules, either there are no rules or all rules are a followup to another';
            return new JsonResponse($return);
        }

        if ($ruleset->checkIfCyclesExist()) {
            $return['success'] = false;
            $return['errors']['global'] = 
                'Cycles exist within the rule structure';
            return new JsonResponse($return);
        }

        //Save to the database
        $this->container->get('mesd_rule.rules')->getStorageManager()->save($ruleset);

        //Return the response
        return new JsonResponse($return);
    }


    /**
     * Loads the json ruleset
     *
     * @param  Request      $request The request
     *
     * @return JsonResponse          The json response
     */
    public function loadFormAction(Request $request)
    {
        //Get the data from the request
        $rulesetName = $request->request->get('ruleset_name');

        //Load up the ruleset
        $ruleset = $this->container->get('mesd_rule.rules')->load($rulesetName);

        //Convert to array
        $rulesetArray = $this->container->get('mesd_rule.rules')->getStorageManager()->transformToArray($ruleset);

        //Return the ruleset array as a json response
        $response = new Response(json_encode($rulesetArray, JSON_FORCE_OBJECT));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
