<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Entity\CategoryTask;
use MK\AppBundle\Entity\Task;
use MK\AppBundle\Form\CategoryTaskType;
use MK\AppBundle\Repository\CategoryTaskRepository;
use MK\AppBundle\Repository\TaskRepository;
use MK\AppBundle\Utils\CategoryTaskPermissions;
use MK\AppBundle\Utils\TaskPermissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Config\Definition\Exception\Exception;
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
        $results = $taskRepository->allFromCategory($currentUser, $category);

        $render = $this->render('MKAppBundle::category/show-ajax-tasks.html.twig', array(
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

    /**
     * @Route("/position",  name="categories_position")
     * @Method("POST")
     */
    public function saveCategoriesPositionAction(Request $request)
    {
        $tc = new CategoryTaskPermissions();
        $tp = new TaskPermissions();

        $response = array('status' => 1);
        $orderCat = $request->get('order');


        $orderCat = explode(',', $orderCat);


        if (!empty($orderCat) && is_array($orderCat)) {
            try {
                $i = 0;
                $em = $this->getDoctrine()->getManager();
                /** @var $repositoryCategory CategoryTaskRepository */
                $repositoryCategory = $this->getDoctrine()->getRepository('MKAppBundle:CategoryTask');

                /** @var $repositoryCategory TaskRepository */
                $repositoryTask = $this->getDoctrine()->getRepository('MKAppBundle:Task');
                foreach ($orderCat as $category) {
                    preg_match("/(.*?)\[(.*?)\]/", $category, $matches);

                    if (empty($matches[1])) {
                        continue;
                    }

                    $catId = (int)$matches[1];
                    /** @var $category CategoryTask */
                    $category = $repositoryCategory->find($catId);
                    if ($category && $tc->isCategoryTaskAuthor($category, $this->getUser())) {
                        $i++;
                        $category->setPosition($i);
                        $em->persist($category);

                        $orderTask = explode(';', $matches[2]);
                        $j = 0;

                        foreach ($orderTask as $taskId) {
                            $taskId = (int)$taskId;
                            /** @var $task Task */
                            $task = $repositoryTask->find($taskId);
                            if ($task && $tp->isTaskAuthor($task, $this->getUser())) {
                                $j++;
                                $task->setPosition($j);
                                $task->setCategory($category);
                                $em->persist($task);
                            }
                        }
                    }

                    $em->flush();
                }
            } catch (Exception $e) {
                $response = array(
                    'status' => 0,
                    'message' => $this->get('translator')->trans('category.position.error'),
                );
            }
        }

        return new JsonResponse($response);
    }
}