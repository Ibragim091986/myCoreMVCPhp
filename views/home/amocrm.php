<?php

use AmoCRM\Client\AmoCRMApiClient;

session_start();

define('TOKEN_FILE', \vendor\classes\Core::$homeDir . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'token_info.json');

//https://edilapti.amocrm.ru/oauth2/auth_code?response_type=code&mode=post_message&client_id=237f9f6e-8cbd-4094-a8b5-0c797e1362b9&approved_code=def50200382dc2931a706bb4b9b99227cfe608384f7cb6acebeb72fcbf23299d8ebe8c5f5e1dfa6b5362083eaa92e48a94fa9e2cfb4243e7a9a91b0c223740177b63ffda346f64de90c7621d7f296b9b628d3c70a46c57c26575c9d10d61dd478ce58867ada7d465bc62eceb06e1ad48513dc1b8e3e407eaac89fe16&scope%5B%5D=push_notifications&scope%5B%5D=crm&scope%5B%5D=notifications&redirect_uri=http%3A%2F%2F767f4f4f0110.ngrok.io%2Famocrm&state=507d785fe346c82c0c0b45a4ea2cab07


$clientId = '237f9f6e-8cbd-4094-a8b5-0c797e1362b9';
$clientSecret = 'hne1VrSBEW813h14bkcnKuPDFQG50hge2ICsNFU9oeZ59I8iFLCEXXIIAMrH9Niu';
$redirectUri = 'http://767f4f4f0110.ngrok.io/amocrm';

$apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

if (isset($_GET['referer'])) {
    $apiClient->setAccountBaseDomain($_GET['referer']);
}

//Рисует панель AMOCRM для разрешения доступа с кнопкой
if (!isset($_GET['code'])) {
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth2state'] = $state;
    //генерирует кнопку, если отправить в запросе гет button
    if (isset($_GET['button'])) {
        echo $apiClient->getOAuthClient()->getOAuthButton(
            [
                'title' => 'Установить интеграцию',
                'compact' => true,
                'class_name' => 'className',
                'color' => 'default',
                'error_callback' => 'handleOauthError',
                'state' => $state,
            ]
        );
        die;
    } else {
        //Делает запрос с использованием кода oauth2state из сессии
        $authorizationUrl = $apiClient->getOAuthClient()->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message',
        ]);
        header('Location: ' . $authorizationUrl);
        die;
    }
} elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}

/**
 * Ловим обратный код
 */
try {
    $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);

    if (!$accessToken->hasExpired()) {
        saveToken([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'baseDomain' => $apiClient->getAccountBaseDomain(),
        ]);
    }
} catch (Exception $e) {
    die((string)$e);
}

$ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);

printf('Hello, %s!', $ownerDetails->getName());

function saveToken($accessToken)
{
    if (
        isset($accessToken)
        && isset($accessToken['accessToken'])
        && isset($accessToken['refreshToken'])
        && isset($accessToken['expires'])
        && isset($accessToken['baseDomain'])
    ) {
        $data = [
            'accessToken' => $accessToken['accessToken'],
            'expires' => $accessToken['expires'],
            'refreshToken' => $accessToken['refreshToken'],
            'baseDomain' => $accessToken['baseDomain'],
        ];

        file_put_contents(TOKEN_FILE, json_encode($data));
    } else {
        exit('Invalid access token ' . var_export($accessToken, true));
    }
}

/**
 * @return \League\OAuth2\Client\Token\AccessToken
 */
function getToken()
{
    $accessToken = json_decode(file_get_contents(TOKEN_FILE), true);

    if (
        isset($accessToken)
        && isset($accessToken['accessToken'])
        && isset($accessToken['refreshToken'])
        && isset($accessToken['expires'])
        && isset($accessToken['baseDomain'])
    ) {
        return new \League\OAuth2\Client\Token\AccessToken([
            'access_token' => $accessToken['accessToken'],
            'refresh_token' => $accessToken['refreshToken'],
            'expires' => $accessToken['expires'],
            'baseDomain' => $accessToken['baseDomain'],
        ]);
    } else {
        exit('Invalid access token ' . var_export($accessToken, true));
    }
}