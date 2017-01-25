<?php
namespace arroios\plugins;

use Facebook\Facebook as CoreFacebook;
use Facebook\FacebookRequest;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookApp;
use arroios\plugins\models\Page;

Class Facebook
{
    public $fb;
    public $facebook_id;
    public $facebook_secret;
    public $facebook_permissions = ['email', 'publish_pages'];

    public $token = false;

    function __construct($conf)
    {
        if(!session_id()) {
            session_start();
        }

        $this->facebook_id = @$conf['facebook_id'];
        $this->facebook_secret = @$conf['facebook_secret'];

        if(isset($conf['facebook_permissions']))
            $this->facebook_permissions =  $conf['facebook_permissions'];

        $this->fb = new CoreFacebook([
            'app_id' => $this->facebook_id, // Replace {app-id} with your app id
            'app_secret' => $this->facebook_secret,
            'default_graph_version' => 'v2.2',
        ]);

    }


    public function getLinkToLogin()
    {
        $signal = (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? '&' : '?';
        // pega o link
        $link = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$signal.'action=login';

        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = $this->facebook_permissions; // Optional permissions
        $loginUrl = $helper->getLoginUrl($link, $permissions);

        return $loginUrl;
    }


    public function login()
    {
        $error = false;

        if(isset($_GET['code']) && $_GET['state']) {

            $helper = $this->fb->getRedirectLoginHelper();

            try
            {
                $accessToken = $helper->getAccessToken();
                if (!isset($accessToken))
                {
                    $error = 'Bad request';
                }
                else
                {
                    // The OAuth 2.0 client handler helps us manage access tokens
                    $oAuth2Client = $this->fb->getOAuth2Client();

                    // Get the access token metadata from /debug_token
                    $tokenMetadata = $oAuth2Client->debugToken($accessToken);

                    $this->token = @$accessToken->getValue();
                    $pages = $this->getPages(current($tokenMetadata)['user_id']);

                }
            }
            catch (FacebookResponseException $e)
            {
                // When Graph returns an error
                $error = 'Graph returned an error: ' . $e->getMessage();
            }
        }

        return [
            'error' => $error,
            'token' => $this->token,
            'userId' => $pages['userId'],
            'pages' => $pages['pages']
        ];
    }


    private function getPages($userId)
    {
        try
        {
            $fbApp = new FacebookApp($this->facebook_id, $this->facebook_secret);

            $requestAccount = new FacebookRequest($fbApp, $this->token, 'GET', '/me/accounts', [
                'fields' => 'name,id,access_token',
                'limit' => '100'
            ]);

            $dataAccount = $this->fb->getClient()->sendRequest($requestAccount)->getDecodedBody()['data'];

            $pages = [];
            foreach ($dataAccount as $value)
            {
                $pages[] = new Page($value, $userId);
            }

            return [
                'userId' => $userId,
                'pages' => $pages
            ];

        }
        catch(FacebookResponseException $e)
        {
            return false;
        }
    }

}