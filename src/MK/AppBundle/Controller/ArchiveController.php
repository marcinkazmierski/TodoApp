<?php
namespace MK\AppBundle\Controller;

use MK\AppBundle\Repository\TaskRepository;
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
        /** @var $taskRepository TaskRepository */
        $taskRepository = $this->getDoctrine()->getRepository('MKAppBundle:Task');

        $doneTasks = $taskRepository->getAllDone();

        return $this->render('MKAppBundle::archive/index.html.twig',
            ['tasks' => $doneTasks]
        );
    }
}