<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction() {
        $userGranted = $this->isGranted('ROLE_USER');

        if ($userGranted)
            return $this->redirectToRoute('dash');
        return $this->render('default/index.html.twig');
    }
}
