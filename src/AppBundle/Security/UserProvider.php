<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 7/20/15
 * Time: 2:43 AM
 */

namespace AppBundle\Security;




use AppBundle\Entity\ApiKey;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider  implements OAuthAwareUserProviderInterface, UserProviderInterface {

    use ContainerAwareTrait;

    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Logger
     */
    private $logger;

    function __construct(EntityManager $em, Logger $logger) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
        // get id_token from raw response
        $oAuthToken = $response->getOAuthToken();
        $rawToken = $oAuthToken->getRawToken();
        $idToken = $rawToken['id_token'];

        $user = $this->loadUserByJWTIdToken($idToken, 'browser');
        if ($user->getName() == null) {
            $user->setName($response->getRealName());
            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    public function loadUserByJWTIdToken($idToken, $clientType = 'browser') {
        // verify id_token against Google's public key and decode JWT
        // one may be able to extract the verification part and not need the client, but whatever
        $googleClient = new \Google_Client();
        $googleOauth = new \Google_Auth_OAuth2($googleClient);

        $clientId = null;
        if ($clientType == 'browser')
            $clientId = $this->container->getParameter('google_client_id');
        else if ($clientType == 'service')
            $clientId = $this->container->getParameter('google_service_client_id');
        else if ($clientType == 'native')
            $clientId = $this->container->getParameter('google_chrome_client_id');

        $loginTicket = $googleOauth->verifyIdToken($idToken, $clientId);
        $payload = $loginTicket->getAttributes()['payload'];

        // register user if needed
        $foundUser = $this->em->getRepository('AppBundle:User')->findOneBy(array('sub' => $payload['sub']));

        if (null == $foundUser) {
            $this->logger->info('User not found, creating '.$payload['email']);
            $user = new User();
            $user->setSub($payload['sub']);
            $user->setEmail($payload['email']);

            $akey = $this->container->get('form.csrf_provider')->generateCsrfToken('apikey');

            $apikey = new ApiKey($akey, $user);
            $user->setApikey($apikey);
            $this->em->persist($user);
            $this->em->persist($apikey);
            $this->em->flush();

            $foundUser = $user;
        }

        $this->logger->info('User found '.$foundUser->getUsername());
        return $foundUser;
    }

    public function loadUserByUsername($username) {
        $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => $username));

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf("Username %s not found", $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof User){
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'AppBundle\Entity\User';
    }

}
