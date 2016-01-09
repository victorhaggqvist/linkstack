<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class EditController extends Controller {

    /**
     * @Route("/stack/{itemId}/edit", requirements={"\d+"}, name="edit")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, $itemId) {
        $em = $this->get('doctrine.orm.entity_manager');

        $item = $em->getRepository('AppBundle:Item')->findOneBy(array(
            'id' => $itemId,
            'user' => $this->getUser()
        ));

        if ($item == null) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder($item, array('attr' => array('class' => 'form-horizontal')))
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('url', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('tags', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('submit', SubmitType::class, array(
                    'label' => 'Save',
                    'attr' => array('class' => 'btn btn-default'))
            )
            ->add('submit_return', SubmitType::class, array(
                    'label' => 'Save and return',
                    'attr' => array('class' => 'btn btn-primary'))
            )
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($item);
            $em->flush();

            if ($form->get('submit_return')->isClicked()) {
                return $this->redirectToRoute('dash');
            }
        }

        return $this->render(':stack:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

}
