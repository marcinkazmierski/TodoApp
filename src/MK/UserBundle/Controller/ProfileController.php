<?php
namespace MK\UserBundle\Controller;

use MK\UserBundle\Form\ProfileType;
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
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $userData = $form->getData();

            $validator = $this->get('validator');
            $errors = $validator->validate($userData);

            if (count($errors) === 0) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($userData);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Updated profile!'
                );
                return $this->redirectToRoute('user_profile');
            } else {
                $this->addFlash(
                    'error',
                    'Invalid data!'
                );
            }
        }

        return $this->render('MKUserBundle::Profile/index.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
}