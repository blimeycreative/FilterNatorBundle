<?php

namespace Savvy\FilterNatorBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Doctrine\ORM\QueryBuilder;

class FilterNatorController
{
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Takes a form and query builder and returns filtered and paginated entities
     * @param QueryBuilder   $filterBuilder
     * @param Form           $form
     * @param string         $session_key
     * @param int            $limit
     * @param int            $page_number
     *
     * @return mixed
     */
    public function filterNate($filterBuilder, &$form, $session_key, $limit = 5, $page_number = 1)
    {
        $em = $this->container->get('doctrine')->getManager();
        $paginator = $this->container->get('knp_paginator');
        $lexik = $this->container->get('lexik_form_filter.query_builder_updater');
        $session = $this->container->get('session');
        $request = $this->container->get('request');

        if ($request->getMethod() == "POST") {
            // bind values from the request
            $form->bind($request);
            // build the query from the given form object
            $session->set("filternator_$session_key", $form->getData());
        } else {
            if ($session->has("filternator_$session_key")) {
                $data = $session->get("filternator_$session_key");
                $this->manageObjects($data, $em);
                $form->setData($data);
            }
        }
        $lexik->addFilterConditions($form, $filterBuilder);
        $entities = $filterBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $entities,
            $request->query->get('page', $page_number) /*page number*/,
            $limit /* Limit entities */
        );

        return $pagination;
    }

    /**
     * Takes an array of objects and merges them into the entity manager
     * Can take nested arrays and ArrayCollections
     * Also refreshes objects as data is sometimes lost
     *
     * @param $data_array
     * @param $em
     */
    public function manageObjects(&$data_array, $em)
    {
        foreach ($data_array as $key => &$value) {
            if (is_array($value) || $value instanceof ArrayCollection) {
                $this->manageObjects($value, $em);
            } else {
                if (is_object($value)) {
                    $data_array[$key] = $em->merge($value);
                    $em->refresh($value);
                }
            }
        }
    }
}
