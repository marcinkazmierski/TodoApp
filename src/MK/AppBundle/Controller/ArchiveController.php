<?php
namespace MK\AppBundle\Controller;

use MK\AppBundle\Repository\TaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    /**
     * @Route("/archive/all",  name="all_archive_tasks")
     * @Method("GET")
     * @Security("has_role('CUSTOMER')")
     */
    public function allArchiveTasks()
    {
        /** @var $taskRepository TaskRepository */
        $taskRepository = $this->getDoctrine()->getRepository('MKAppBundle:Task');

        $doneTasks = $taskRepository->getAllDone();

        $render =  $this->render('MKAppBundle::archive/all.html.twig',
            ['tasks' => $doneTasks]
        );

        $response = array(
            'message' => '',
            'status' => 1,
            'content' => $render->getContent()
        );
        return new JsonResponse($response);
    }
}