<?php
namespace MK\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ArchiveController extends Controller
{
    /**
     * @Route("/archive",  name="archive_tasks")
     * @Method("GET")
     * @Security("has_role('CUSTOMER')")
     */
    public function indexAction()
    {
        return $this->render('MKAppBundle::archive/index.html.twig');
    }
}