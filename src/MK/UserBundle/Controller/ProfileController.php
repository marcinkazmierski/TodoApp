<?php


namespace MK\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ProfileController extends Controller
{
    /**
     * @Route("/profile",  name="user_profile")
     * @Route("/profile/")
     * @Security("has_role('CUSTOMER')")
     */
    public function indexAction(Request $request)
    {
        return $this->render('MKUserBundle::Profile/index.html.twig', array(
            'user' => $this->getUser()
        ));
    }
}