<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 7/20/15
 * Time: 1:19 AM
 */

namespace AppBundle\Menu;


use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class Builder implements ContainerAwareInterface {

    // Container via trait don't seem to work with Tiwg, using interface meanwhile
    //    use ContainerAwareTrait;
    /** @var  ContainerInterface */
    private $container;

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

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}
