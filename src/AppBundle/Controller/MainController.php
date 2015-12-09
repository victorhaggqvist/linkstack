<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction() {
        try {
            $userGranted = $this->isGranted('ROLE_USER');

            if ($userGranted)
                return $this->redirectToRoute('dash');
        } catch (AuthenticationCredentialsNotFoundException $e) {

        }
        return $this->render('default/index.html.twig');
    }
}
