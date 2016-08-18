<?php

namespace MK\AppBundle\Controller;

use MK\AppBundle\Entity\CategoryTask;
use MK\AppBundle\Form\CategoryTaskType;
use MK\AppBundle\Utils\CategoryTaskPermissions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/category")
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
            $category = $form->getData();

            $category->setStatus(1);
            $category->setUser($this->getUser());

            $validator = $this->get('validator');
            $errors = $validator->validate($category);
            if (count($errors) === 0) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Added new category!'
                );
                return $this->redirectToRoute('new_category');
            } else {
                $this->addFlash(
                    'error',
                    'Invalid category data!'
                );
            }
        }

        return $this->render('MKAppBundle::category/new.html.twig', array(
            'form' => $form->createView(),
        ));
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
        $query = $this->getDoctrine()->getRepository('MKAppBundle:Task')->queryAllFromCategory($currentUser, $category);

        $paginator = $this->get('knp_paginator');

        $tasks = $paginator->paginate(
            $query,
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
}