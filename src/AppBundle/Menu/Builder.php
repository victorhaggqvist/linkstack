<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 7/20/15
 * Time: 1:19 AM
 */

namespace AppBundle\Menu;


use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class Builder extends ContainerAware {

    public function mainMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav nav-pills');

        try {
            $userGranted = $this->container->get('security.authorization_checker')->isGranted('ROLE_USER');

            if ($userGranted) {
                $menu->addChild('Home', array('route' => 'dash'));
                $menu->addChild('Browse', array('route' => 'browse'));

                $menu->addChild('Sign out', array('route' => 'logout'))->setAttribute('class', 'pull-right');

                $token = $this->container->get('security.token_storage')->getToken();
                $name = $token->getUser()->getName();
                $avatar = $token->getUser()->getPictureUrl();
                $menu->addChild('', array(
                    'extras' => array(
                        'img' => $avatar,
                    ),
                ))->setAttribute('class', 'pull-right');
                $menu->addChild($name, array('extras' => array('label' => true)))->setAttribute('class', 'pull-right');
            } else {
                $menu->addChild('Home', array('route' => 'homepage'));
                $menu->addChild('Login with Google', array('uri' => '/connect/google', 'extras' => array('connect' => true)));
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {
            $menu->addChild('Home', array('route' => 'homepage'));
            $menu->addChild('Login with Google', array('uri' => '/connect/google', 'extras' => array('connect' => true)));
        }

        return $menu;
    }

}