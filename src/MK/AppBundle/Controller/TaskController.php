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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/task")
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
            $task = $form->getData();
            $task->setStatus(1);
            $task->setUser($this->getUser());
            $task->setCreated(new \DateTime('now'));
            $validator = $this->get('validator');
            $errors = $validator->validate($task);
            if (count($errors) === 0) {
                $category = $task->getCategory();
                $em = $this->getDoctrine()->getManager();

                $task->setDeadline(new \DateTime($task->getDeadline()));

                $em->persist($task);
                $em->persist($category);
                $em->flush();

                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('task.added_new')
                );

                return $this->redirectToRoute('new_task');
            } else {
                $this->addFlash(
                    'error',
                    $this->get('translator')->trans('task.invalid_data')
                );
            }
        }

        return $this->render('MKAppBundle::task/new-task.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/all", name="all_tasks")
     */
    public function allAction(Request $request)
    {
        $currentUser = $this->getUser();
        $query = $this->getDoctrine()->getRepository('MKAppBundle:Task')->queryAll($currentUser);

        $paginator = $this->get('knp_paginator');

        $tasks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('MKAppBundle::task/all.html.twig', array(
            'tasks' => $tasks
        ));
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
        $form->add('save', SubmitType::class, array('label' => 'Save'));

        $form->add('deadline', TextType::class, array(
            'data' => $task->getDeadline()->format('d.m.Y H:i')
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $task->setDeadline(new \DateTime($task->getDeadline()));
            $validator = $this->get('validator');
            $errors = $validator->validate($task);
            if (count($errors) === 0) {
                $category = $task->getCategory();
                $em = $this->getDoctrine()->getManager();
                $em->persist($task);
                $em->persist($category);
                $em->flush();

                $this->addFlash(
                    'success',
                    $this->get('translator')->trans('task.edit_success')
                );

                return $this->redirectToRoute('edit_task', array('id' => $task->getId()));
            }
        }
        return $this->render('MKAppBundle::task/edit.html.twig', array(
            'task' => $task,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/archives", name="archives_tasks")
     */
    public function archivesAction(Request $request)
    {
        $currentUser = $this->getUser();
        $query = $this->getDoctrine()->getRepository('MKAppBundle:Task')->queryAll($currentUser, 2);

        $paginator = $this->get('knp_paginator');

        $tasks = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('MKAppBundle::task/archives.html.twig', array(
            'tasks' => $tasks
        ));
    }
}