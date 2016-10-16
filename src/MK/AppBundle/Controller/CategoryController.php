<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Entity\CategoryTask;
use MK\AppBundle\Form\CategoryTaskType;
use MK\AppBundle\Repository\TaskRepository;
use MK\AppBundle\Utils\CategoryTaskPermissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/ajax/category")
 * @Security("has_role('CUSTOMER')")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/new", name="new_category")
     */
    public function newAction(Request $request)
    {
        $category = new CategoryTask();
        $form = $this->createForm(CategoryTaskType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $response = array();

            /** @var $category CategoryTask */
            $category = $form->getData();

            $category->setStatus(1);
            $category->setUser($this->getUser());

            $validator = $this->get('validator');
            $errors = $validator->validate($category);
            if (count($errors) === 0) {
                $em = $this->getDoctrine()->getManager();

                try {
                    $em->persist($category);
                    $em->flush();

                    $response = array(
                        'message' => $this->get('translator')->trans('category.added_new <i>%name%</i>', array('%name%' => $category->getName())),
                        'status' => 1,
                        'content' => ''
                    );
                } catch (\Exception $e) {
                    $response = array(
                        'message' => $this->get('translator')->trans('category.something_wrong'),
                        'status' => 0,
                        'content' => ''
                    );
                }
            } else {
                $response = array(
                    'message' => $this->get('translator')->trans('category.invalid_data'),
                    'status' => 0,
                    'content' => ''
                );
            }
            return new JsonResponse($response);
        }

        $render = $this->render('MKAppBundle::category/new-category.html.twig', array(
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
     * @Route("/all", name="all_categories")
     */
    public function allAction(Request $request)
    {
        $currentUser = $this->getUser();
        $query = $this->getDoctrine()->getRepository('MKAppBundle:CategoryTask')->queryAll($currentUser);

        $paginator = $this->get('knp_paginator');

        $categories = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('MKAppBundle::category/all.html.twig', array(
            'categories' => $categories
        ));
    }

    /**
     * @Route("/{id}", name="show_category")
     */
    public function showAction(Request $request, CategoryTask $category)
    {
        $tc = new CategoryTaskPermissions();

        if (!$tc->isCategoryTaskAuthor($category, $this->getUser())) {
            throw $this->createAccessDeniedException($this->get('translator')->trans('access.denied.text'));
        }

        $currentUser = $this->getUser();
        $results = $this->getDoctrine()->getRepository('MKAppBundle:Task')->allFromCategory($currentUser, $category);
        $paginator = $this->get('knp_paginator');
        $tasks = $paginator->paginate(
            $results,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('MKAppBundle::category/showTasks.html.twig', array(
            'tasks' => $tasks
        ));
    }

    /**
     * @Route("/{id}/edit", name="edit_category")
     */
    public function editAction(Request $request, CategoryTask $category)
    {
        $tc = new CategoryTaskPermissions();

        if (!$tc->isCategoryTaskAuthor($category, $this->getUser())) {
            throw $this->createAccessDeniedException($this->get('translator')->trans('access.denied.text'));
        }

        $form = $this->createForm(CategoryTaskType::class, $category);
        $form->add('save', SubmitType::class, array('label' => 'Save'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $category CategoryTask */
            $category = $form->getData();

            $validator = $this->get('validator');
            $errors = $validator->validate($category);
            if (count($errors) === 0) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Updated category!'
                );
                return $this->redirectToRoute('edit_category', array('id' => $category->getId()));
            } else {
                $this->addFlash(
                    'error',
                    'Invalid category data!'
                );
            }
        }

        return $this->render('MKAppBundle::category/edit.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/ajax/{id}", name="show_ajax_category")
     */
    public function getTasksFromCategoryAction(Request $request, CategoryTask $category)
    {
        $tc = new CategoryTaskPermissions();

        if (!$tc->isCategoryTaskAuthor($category, $this->getUser())) {
            throw $this->createAccessDeniedException($this->get('translator')->trans('access.denied.text'));
        }

        $currentUser = $this->getUser();

        /** @var $taskRepository TaskRepository */
        $taskRepository = $this->getDoctrine()->getRepository('MKAppBundle:Task');
        $results = $taskRepository->allFromCategory($currentUser, $category, 5);

        $render = $this->render('MKAppBundle::category/showAjaxTasks.html.twig', array(
            'tasks' => $results,
            'category' => $category
        ));

        $response = array(
            'message' => '',
            'status' => 1,
            'content' => $render->getContent()
        );
        return new JsonResponse($response);
    }
}