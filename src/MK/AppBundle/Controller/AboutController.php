<?php

namespace MK\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AboutController extends Controller
{
    /**
     * @Route("/about",  name="about_page")
     * @Method("GET")
     * @Security("has_role('CUSTOMER')")
     */
    public function indexAction()
    {
        return $this->render('MKAppBundle::about/index.html.twig');
    }
}