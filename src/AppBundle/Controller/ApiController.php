<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Item;
use GuzzleHttp\Client;
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
    public function listItemsAction(Request $request) {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        $itemsPerPage = 30;
        $page = $request->query->getInt('page', 1);
        $pageOffset = $itemsPerPage * ($page-1);
        $pageLimitEnd = $itemsPerPage * $page;

        $tag = $request->query->getAlnum('tag', null);
        $queryString = $request->query->get('query', null);

        $em = $this->get('doctrine.orm.entity_manager');
        if ($queryString != null) {
            $dql   = "SELECT i FROM AppBundle:Item i
                      WHERE i.user = :user AND (i.title LIKE :query OR i.url LIKE :query OR i.tags LIKE :query)
                      ORDER BY i.created DESC";
            $query = $em->createQuery($dql)
                ->setFirstResult($pageOffset)
                ->setMaxResults($itemsPerPage)
                ->setParameter('user', $user)
                ->setParameter('query', '%'.$queryString.'%');
            $items = $query->execute();
        } else if ($tag != null) {
            $items = $repo = $em->getRepository('AppBundle:Item')
                ->findBy(array(
                    'user' => $user,
                    'tags' => '%'.$tag.'%'
                ), array('created' => 'desc'), $itemsPerPage, $pageOffset);
        }
        else {
            $items = $repo = $em->getRepository('AppBundle:Item')
                ->findBy(array(
                    'user' => $user
                ), array('created' => 'desc'), $itemsPerPage, $pageOffset);
        }



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
//            return new JsonResponse(array('message' => sprintf("User '%d' not allowed to access Item '%s'", $user->getId(), $id)), 403);
        }
    }

    /**
     * @Route("/api/items/{id}", name="api_update_item")
     * @Method("PUT")
     */
    public function updateItemAction(Request $request, $id) {
        $token = $this->get('security.token_storage')->getToken();
        $user = $token->getUser();

        $em = $this->get('doctrine.orm.entity_manager');
        $item = $em->getRepository("AppBundle:Item")->findOneBy(array('user' => $user, 'id' => $id));

        if (null == $item) {
            return new Response('', 403);
        }

        $json = json_decode($request->getContent(), false);

        if ($json['title'])
            $item->setTitle($json['title']);
        if ($json['url'])
            $item->setTitle($json['url']);
        if ($json['tags'])
            $item->setTitle($json['tags']);

        $item->updateModified();

        $em->persist($item);
        $em->flush();

        return new Response('', 204);
    }

    /**
     * Return apikey by JWT
     * @Route("/api/key", name="api_get_key")
     * @Method("POST")
     */
    public function getApiKeyAction(Request $request) {
        $idToken = $request->getContent();

        $userProvider = $this->get('app.security.user_provider');
        try {
            $user = $userProvider->loadUserByJWTIdToken($idToken, 'service');
        } catch(\Google_Auth_Exception $e) {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }

        return new JsonResponse(array('key' => $user->getApikey()->getAkey()));
    }

    /**
     * @Route("/api/key", name="api_get_key_client")
     * @Method("GET")
     */
    public function getClientKeyAction(Request $request) {
        /**
         * $code is the 'Success code' which is returned in the title bar
         * from a call to https://accounts.google.com/o/oauth2/auth with redirect_uri = urn:ietf:wg:oauth:2.0:oob:auto
         */
        $code = $request->query->get('code', null);

        if (null == $code)
            return new Response('', 403);

        $clientId = $this->container->getParameter('google_chrome_client_id');
        $clientSecret = $this->container->getParameter('google_chrome_secret');

        $client = new Client();
        $resp = $client->post('https://accounts.google.com/o/oauth2/token', [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => 'urn:ietf:wg:oauth:2.0:oob:auto'
            ]
        ]);

        if ($resp->getStatusCode() != 200)
            return new Response('', 403);

        $body = $resp->getBody()->getContents();
        $json = json_decode($body, true);
        $idToken = $json['id_token'];

        $userProvider = $this->get('app.security.user_provider');
        try {
            $user = $userProvider->loadUserByJWTIdToken($idToken, 'native');
        } catch(\Google_Auth_Exception $e) {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }

        return new JsonResponse(array('key' => $user->getApikey()->getAkey()));
    }
}
