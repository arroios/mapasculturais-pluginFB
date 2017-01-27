<?php
namespace arroios\plugins;

use Facebook\Facebook as CoreFacebook;
use Facebook\FacebookRequest;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookApp;
use arroios\plugins\models\Page;
use arroios\plugins\models\Event;

Class Facebook
{
    public $fb;
    public $facebook_id;
    public $facebook_secret;
    public $facebook_permissions = ['email', 'publish_pages', 'manage_pages'];

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
        $link = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$signal.'plugin-facebook-action=login';

        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = $this->facebook_permissions; // Optional permissions
        $loginUrl = $helper->getLoginUrl($link, $permissions);

        return $loginUrl;
    }


    public function login($config)
    {
        $error = false;

        if(isset($_GET['code']) && $_GET['state']) {

            try
            {
                $helper = $this->fb->getRedirectLoginHelper();
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



                    $fbApp = new FacebookApp($this->facebook_id, $this->facebook_secret);
                    $requestAccount = new FacebookRequest($fbApp, $accessToken->getValue(), 'GET', '/oauth/access_token', [
                        'grant_type' => 'fb_exchange_token',
                        'fb_exchange_token' => $accessToken->getValue(),
                        'client_id' => $this->facebook_id,
                        'client_secret' => $this->facebook_secret
                    ]);

                    $this->token = $this->fb->getClient()->sendRequest($requestAccount)->getDecodedBody()['access_token'];
                    $pages = $this->getPages($config, current($tokenMetadata)['user_id']);

                }
            }
            catch (FacebookResponseException $e)
            {
                return false;
            }
        }

        return [
            'error' => $error,
            'token' => $this->token,
            'userId' => $pages['userId'],
            'pages' => $pages['pages']
        ];
    }


    public function getPages($config, $userId)
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
                $page = new Page($config['Page']);
                $page->load($value);
                $pages[] = $page->getInfo();
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

    public function getEvents($config, $pageData)
    {
        try
        {
            $page = new Page($config['Page']);
            $page->load($pageData);
            $page->create();


            $fbApp = new FacebookApp($this->facebook_id, $this->facebook_secret);

            $requestAccount = new FacebookRequest($fbApp, $page->facebookToken, 'GET', '/'.$page->facebookPageId.'/events', [
                'fields' => 'start_time,end_time,name,description,id,updated_time,cover,place',
                'limit' => '100'
            ]);

            $data = $this->fb->getClient()->sendRequest($requestAccount)->getDecodedBody()['data'];

            $events = [];
            foreach ($data as $value)
            {
                $__temp = new Event($config['Event']);
                $__temp->load($value, $page->facebookPageId);
                $__temp->create();

                $events[] = $__temp->getInfo();
            }

            return [
                'pageId' => $page->facebookPageId,
                'events' => $events
            ];
        }
        catch(FacebookResponseException $e)
        {
            return false;
        }
    }

}