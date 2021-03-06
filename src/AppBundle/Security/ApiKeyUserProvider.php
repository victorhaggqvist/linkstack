<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 7/21/15
 * Time: 3:22 AM
 */

namespace AppBundle\Security;


use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyUserProvider implements UserProviderInterface {

    use ContainerAwareTrait;

    /**
     * @var Logger
     */
    private $logger;


    /**
     * ApiKeyUserProvider constructor.
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function getUsernameForApiKey($apiKey) {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $key = $em->getRepository('AppBundle:ApiKey')->findOneBy(array('akey' => $apiKey));

        if (null == $key)
            return false;

        // Look up the username based on the token in the database, via
        // an API call, or do something entirely different
        $username = $key->getUser()->getUsername();

        return $username;
    }

    public function loadUserByUsername($username) {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $username));

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf("Username %s not found", $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class) {
        return 'AppBundle\Entity\User' === $class;
    }
}
