<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Entity\Task;
use MK\AppBundle\Form\TaskType;
use MK\AppBundle\Utils\TaskPermissions;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/ajax/task")
 * @Security("has_role('CUSTOMER')")
 */
class TaskController extends Controller
{

    /**
     * @Route("/new", name="new_task")
     */
    public function newAction(Request $request)
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task, array('user' => $this->getUser()));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $response = array();
            /** @var $task Task */
            $task = $form->getData();
            $task->setStatus(1);
            $task->setUser($this->getUser());
            $task->setCreated(new \DateTime('now'));
            $validator = $this->get('validator');
            $errors = $validator->validate($task);
            if (count($errors) === 0) {
                $category = $task->getCategory();
                $em = $this->getDoctrine()->getManager();

                if ($task->getDeadline()) {
                    $task->setDeadline(new \DateTime($task->getDeadline()));
                }

                try {
                    $em->persist($task);
                    $em->persist($category);
                    $em->flush();

                    $response = array(
                        'message' => $this->get('translator')->trans('task.added_new <i>%name%</i>', array('%name%' => $task->getTitle())),
                        'status' => 1,
                        'content' => ''
                    );
                } catch (\Exception $e) {
                    $response = array(
                        'message' => $this->get('translator')->trans('task.something_wrong'),
                        'status' => 0,
                        'content' => ''
                    );
                }
            } else {
                $response = array(
                    'message' => $this->get('translator')->trans('task.invalid_data'),
                    'status' => 0,
                    'content' => ''
                );
            }
            return new JsonResponse($response);
        }

        $render = $this->render('MKAppBundle::task/new-task.html.twig', array(
            'form' => $form->createView(),
        ));

        $response = array(
            'message' => '',
            'status' => 1,
            'content' => $render->getContent()
        );

        return new JsonResponse($response);
    }


    /**
     * @Route("/{id}/edit", name="edit_task")
     */
    public function editAction(Request $request, Task $task)
    {
        $tp = new TaskPermissions();

        if (!$tp->isTaskAuthor($task, $this->getUser())) {
            throw $this->createAccessDeniedException($this->get('translator')->trans('access.denied.text'));
        }

        $form = $this->createForm(TaskType::class, $task, array('user' => $this->getUser()));

        if ($task->getDeadline()) {
            $form->add('deadline', TextType::class, array(
                'data' => $task->getDeadline()->format('d.m.Y H:i')
            ));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $task Task */
            $task = $form->getData();
            if ($task->getDeadline()) {
                $task->setDeadline(new \DateTime($task->getDeadline()));
            }
            $validator = $this->get('validator');
            $errors = $validator->validate($task);
            if (count($errors) === 0) {
                if ($task->getStatus() === 2) {
                    $task->setStatus(1);
                }
                $category = $task->getCategory();
                $em = $this->getDoctrine()->getManager();
                try {
                    $em->persist($task);
                    $em->persist($category);
                    $em->flush();

                    $response = array(
                        'message' => $this->get('translator')->trans('task.edit_success'),
                        'status' => 1,
                        'content' => ''
                    );
                } catch (\Exception $e) {
                    $response = array(
                        'message' => $this->get('translator')->trans('task.something_wrong'),
                        'status' => 0,
                        'content' => ''
                    );
                }
            } else {
                $response = array(
                    'message' => $this->get('translator')->trans('task.invalid_data'),
                    'status' => 0,
                    'content' => ''
                );
            }
            return new JsonResponse($response);
        }
        $render = $this->render('MKAppBundle::task/new-task.html.twig', array(
            'form' => $form->createView(),
        ));

        $response = array(
            'message' => '',
            'status' => 1,
            'content' => $render->getContent()
        );

        return new JsonResponse($response);
    }

    /**
     * @Route("/{id}/done",  name="task_done")
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
     * @Route("/{id}/delete",  name="task_delete")
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
}