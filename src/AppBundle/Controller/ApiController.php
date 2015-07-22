<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller {

    /**
     * @Route("/api/items", name="api_create")
     * @Method("POST")
     */
    public function createItemAction(Request $request) {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();
        $item = new Item();
        $item->setUser($user);

        $jsonBody = json_decode($request->getContent(), true);
        $item->setTitle($jsonBody['title']);
        $item->setUrl($jsonBody['url']);
        $item->setTags($jsonBody['tags']);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($item);
        $em->flush();

        return new JsonResponse(['id' => $item->getId()]);
    }


    /**
     * @Route("/api/items", name="api_list")
     * @Method("GET")
     */
    public function listItemsAction() {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        $em = $this->get('doctrine.orm.entity_manager');
        $items = $repo = $em->getRepository('AppBundle:Item')->findBy(array('user' => $user), array('created' => 'desc'), 30);

        $json = [];
        foreach ($items as $i) {
            $json[] = $i->toJson();
        }

        return new JsonResponse($json);
    }

    /**
     * @Route("/api/items/{id}", name="api_delete_item")
     * @Method("DELETE")
     */
    public function deleteItemAction($id) {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        $em = $this->get('doctrine.orm.entity_manager');
        $item = $repo = $em->getRepository('AppBundle:Item')->findOneBy(array('user' => $user, 'id' => $id));
        if (null !== $item) {
            $em->remove($item);
            $em->flush();

            return new Response('', 204);
        } else {
            return new Response('', 403);
        }
    }

    /**
     * Return apikey by JWT
     * @Route("/api/key", name="api_get_key")
     * @Method("POST")
     */
    public function getApiKeyAction(Request $request) {
        $idToken = $request->getContent();

        $userProvider = $this->get('app.security.user_provider');
        $user = $userProvider->loadUserByJWTIdToken($idToken);

        return new JsonResponse(array('key' => $user->getApikey()->getAkey()));
    }
}
