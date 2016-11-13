<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Form\ContactType;
use MK\MailBundle\Service\MailTemplate;
use MK\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AjaxController
 * @package MK\AppBundle\Controller
 * @Route("/ajax/contact")
 * @Security("has_role('CUSTOMER')")
 */
class ContactController extends Controller
{

    /**
     * @Route("/contact",  name="ajax_contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $subject = $data['subject'];
                /** @var $currentUser User */
                $currentUser = $this->getUser();
                $params = array(
                    'message' => $data['message'],
                    'author' => $currentUser->getUsername()
                );
                /** @var $serviceMail MailTemplate */
                $serviceMail = $this->get('mk_mail_engine.class');
                $serviceMail->sendMailWithTemplate($subject, $params, $currentUser->getEmail(), 'contact');
                // TODO:
                // http://www.lucas.courot.com/how-to-create-a-contact-form-using-symfony2.html

                $response = array(
                    'message' => $this->get('translator')->trans('contact.success'),
                    'status' => 1,
                    'content' => ''
                );
            } else {
                $response = array(
                    'message' => $this->get('translator')->trans('contact.invalid_data'),
                    'status' => 0,
                    'content' => ''
                );
            }

            return new JsonResponse($response);
        }

        $render = $this->render('MKAppBundle::ajax/contact.html.twig', array(
            'form' => $form->createView()
        ));

        $response = array(
            'message' => '',
            'status' => 1,
            'content' => $render->getContent()
        );

        return new JsonResponse($response);
    }
}
