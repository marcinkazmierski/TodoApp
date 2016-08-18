<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Entity\Task;
use MK\AppBundle\Utils\TaskPermissions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AjaxController
 * @package MK\AppBundle\Controller
 * @Route("/ajax")
 * @Security("has_role('CUSTOMER')")
 */
class AjaxController extends Controller
{
    /**
     * @Route("/done/{id}",  name="task_done")
     * @Method("POST")
     */
    public function indexAction(Request $request, Task $task)
    {
        $tp = new TaskPermissions();

        if (!$tp->isTaskAuthor($task, $this->getUser())) {

            $response = array(
                'message' => $this->get('translator')->trans('access.denied.text'),
                'status' => 0,
            );
        } else {
            $task->setStatus(2);
            $task->setDoneDate(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $response = array(
                'message' => $this->get('translator')->trans('ajax.task_done'),
                'status' => 1,
            );
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }


    /**
     * @Route("/delete/{id}",  name="task_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, Task $task)
    {
        $tp = new TaskPermissions();

        if (!$tp->isTaskAuthor($task, $this->getUser())) {

            $response = array(
                'message' => $this->get('translator')->trans('access.denied.text'),
                'status' => 0,
            );
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            $response = array(
                'message' => $this->get('translator')->trans('ajax.task_done'),
                'status' => 1,
            );
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Parse request data.
     */
    private function parseRequest(Request $request)
    {
        $data = $request->request->all();
        if (!is_array($data) || empty($data)) {
            return false;
        }
        return $data;
    }

}