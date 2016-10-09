<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Repository\CategoryTaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class IndexController extends Controller
{
    /**
     * @Route("/",  name="homepage")
     * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="task_index_paginated")
     * @Method("GET")
     * @Security("has_role('CUSTOMER')")
     */
    public function indexAction(Request $request)
    {
        // $locale = $request->getLocale();
        // dump($locale);
        $currentUser = $this->getUser();
        /** @var $categoryTaskRepository CategoryTaskRepository */
        $categoryTaskRepository = $this->getDoctrine()->getRepository('MKAppBundle:CategoryTask');
        $categories = $categoryTaskRepository->queryAll($currentUser);

dump($categories);
        return $this->render('MKAppBundle::index/index.html.twig', array(
            'categories' => $categories
        ));
    }
}
