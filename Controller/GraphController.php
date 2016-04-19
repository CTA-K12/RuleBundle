<?php

namespace Mesd\RuleBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

class GraphController extends ContainerAware
{
    /**
     * Displays the form.
     *
     * @param string $rulesetName The url encoded name of the ruleset to display
     *
     * @return Response The form
     */
    public function graphAction($encodedRulesetName)
    {
        //Decode the ruleset name
        $rulesetName = urldecode($encodedRulesetName);
        $ruleset     = $this->container->get('mesd_rule.rules')->load($rulesetName);

        //Render the form page
        return new Response($this->container->get('templating')->render('MesdRuleBundle:Graph:graph.html.twig', [
            'adjacencyList' => $ruleset->getRelatedList(),
        ]));
    }
}
