<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Entity\CategoryTask;
use MK\AppBundle\Form\CategoryTaskType;
use MK\AppBundle\Repository\CategoryTaskRepository;
use MK\AppBundle\Repository\TaskRepository;
use MK\AppBundle\Utils\CategoryTaskPermissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
        /** @var $repository CategoryTaskRepository */
        $repository = $this->getDoctrine()->getRepository('MKAppBundle:CategoryTask');
        $categories = $repository->queryAll($currentUser);

        $render = $this->render('MKAppBundle::category/all.html.twig', array(
            'categories' => $categories
        ));

        $response = array(
            'message' => '',
            'status' => 1,
            'content' => $render->getContent()
        );
        return new JsonResponse($response);
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

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $category CategoryTask */
            $category = $form->getData();

            $validator = $this->get('validator');
            $errors = $validator->validate($category);
            if (count($errors) === 0) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($category);
                    $em->flush();

                    $response = array(
                        'message' => $this->get('translator')->trans('category.edit_success'),
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
     * @Route("/{id}/show", name="show_ajax_category")
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

    /**
     * @Route("/{id}/delete",  name="category_delete")
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
            /** @var $taskRepository TaskRepository */
            $taskRepository = $this->getDoctrine()->getRepository('MKAppBundle:Task');
            $results = $taskRepository->allFromCategory($this->getUser(), $category, 1);

            if (count($results) > 0) {
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
        return new JsonResponse($response);
    }
}