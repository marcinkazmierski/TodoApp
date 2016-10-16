<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Entity\CategoryTask;
use MK\AppBundle\Entity\Task;
use MK\AppBundle\Utils\CategoryTaskPermissions;
use MK\AppBundle\Utils\TaskPermissions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        return new JsonResponse($response);
    }


    /**
     * @Route("/delete/task/{id}",  name="task_delete")
     * @Method("POST")
     */
    public function deleteTaskAction(Request $request, Task $task)
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

            $page = $request->request->get('page');

            $action = '';
            if ($page === 'show_task') {
                $action = $this->generateUrl('homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL);
                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('ajax.task_delete_done')
                );
            }

            $response = array(
                'message' => $this->get('translator')->trans('ajax.task_delete_done'),
                'status' => 1,
                'action' => $action
            );
        }
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @Route("/delete/category/{id}",  name="category_delete")
     * @Method("POST")
     */
    public function deleteCategoryAction(Request $request, CategoryTask $category)
    {
        $tc = new CategoryTaskPermissions();

        if (!$tc->isCategoryTaskAuthor($category, $this->getUser())) {
            $response = array(
                'message' => $this->get('translator')->trans('access.denied.text'),
                'status' => 0,
            );
        } else {
            if (count($category->getTasks()) > 0) {
                $response = array(
                    'message' => $this->get('translator')->trans('ajax.category_delete_have_tasks'),
                    'status' => 0,
                );
            } else {
                $em = $this->getDoctrine()->getManager();
                $em->remove($category);
                $em->flush();

                $response = array(
                    'message' => $this->get('translator')->trans('ajax.category_delete_done'),
                    'status' => 1,
                );
            }
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
