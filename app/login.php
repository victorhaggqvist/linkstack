<?php

require_once '../include.inc';

use OAuth\OAuth2\Service\Google;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

$uriFactory = new \OAuth\Common\Http\Uri\UriFactory();
$currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
$currentUri->setQuery('');

$serviceFactory = new \OAuth\ServiceFactory();

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    OAUTH_CONSUMER_ID,
    OAUTH_CONSUMER_SECRET,
    OAUTH_CALLBACK
);

// Instantiate the Google service using the credentials, http client and storage mechanism for the token
$googleService = $serviceFactory->createService('google', $credentials, $storage, array('userinfo_email', 'userinfo_profile'));

if (!empty($_GET['code'])) {
  // This is a callback request from google, get the token

  try {
    $authToken = $googleService->requestAccessToken(htmlspecialchars($_GET['code']));
  } catch (\OAuth\Common\Http\Exception\TokenResponseException $e) {
    echo "Old request :/ Go again\n";
    header("Location: ./login.php");
  }

  $accessToken = $authToken->getAccessToken();
  $eol = $authToken->getEndOfLife();

  // Send a request with it
  $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

  $user = \Snilius\Stack\User::getByOAuthId($result['id']);
  if (!$user){
    $user = new \Snilius\Stack\User($result['id'], $result['email'], $result['name'], $result['picture']);
    $user->commit();
  }

  $session = new \Snilius\Login\Session($user->getId());
  $session->commit();
  $session->bakeCookie();
  header('Location: ./index.php');

} else {
  $url = $googleService->getAuthorizationUri();
  header('Location: ' . $url);
}

?>
