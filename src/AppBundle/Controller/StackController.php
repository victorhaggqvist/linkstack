<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StackController extends Controller {

    /**
     * @Route("/stack", name="dash")
     * @Method("GET")
     */
    public function indexAction() {
        $user = $this->getUser();
        $em = $this->get('doctrine.orm.entity_manager');

        $items = $em->getRepository('AppBundle:Item')->findBy(array(
            'user' => $user
        ), array(
            'created' => 'desc'
        ), 20);

        return $this->render(':stack:index.html.twig', array(
            'items' => $items
        ));
    }

    /**
     * @Route("/stack/browse", name="browse")
     */
    public function browseAction(Request $request) {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        $em    = $this->get('doctrine.orm.entity_manager');

        $queryString = $request->get('q');
        $tags = $request->query->getBoolean('tags', false);

        if (null !== $queryString && $tags) {
            $this->get('logger')->info('search query');
            $dql   = "SELECT i FROM AppBundle:Item i WHERE i.user = :user AND (i.tags LIKE :query) ORDER BY i.created DESC";
            $query = $em->createQuery($dql)
                ->setParameter('user', $user)
                ->setParameter('query', '%'.$queryString.'%');
        } elseif (null !== $queryString) {
            $this->get('logger')->info('search query');
            $dql   = "SELECT i FROM AppBundle:Item i WHERE i.user = :user AND (i.title LIKE :query OR i.url LIKE :query OR i.tags LIKE :query) ORDER BY i.created DESC";
            $query = $em->createQuery($dql)
                ->setParameter('user', $user)
                ->setParameter('query', '%'.$queryString.'%');
        } else {
            $this->get('logger')->info('list query');
            $dql   = "SELECT i FROM AppBundle:Item i WHERE i.user = :user ORDER BY i.created DESC";
            $query = $em->createQuery($dql)
                ->setParameter('user', $user);
        }


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('itemsPerPage', 30)/*limit per page*/
        );

        return $this->render(':stack:browse.html.twig', array(
            'pagination' => $pagination,
            'query' => $queryString,
            'tagsOnly' => $tags
        ));
    }
}
