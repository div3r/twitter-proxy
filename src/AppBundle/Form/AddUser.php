<?php

// src/AppBundle/Form/AddUser.php
namespace AppBundle\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AddUser extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, array('label' => false, 'attr' => array('class' => 'form-control mr-sm-2')))
            ->add('save', SubmitType::class, array('label' => 'Add user', 'attr' => array('class' => 'btn btn-outline-success my-2 my-sm-0')))
        ;
    }

    public function processForm(Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render('task/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
