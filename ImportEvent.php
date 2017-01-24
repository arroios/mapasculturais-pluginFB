<?php
/**
 * Created by PhpStorm.
 * User: jhans
 * Date: 24/01/2017
 * Time: 13:59
 */

namespace arroios\plugins;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

Class ImportEvent
{
    public $fb;
    public $facebook_id;
    public $facebook_secret;
    public $facebook_permissions = ['email'];

    public $templateTag = '<a class="arroios-bt" href="#link#">Importar eventos do facebook</a>';

    function __construct($conf)
    {
        if(!session_id()) {
            session_start();
        }

        $this->facebook_id = @$conf['facebook_id'];
        $this->facebook_secret = @$conf['facebook_secret'];

        if(isset($conf['facebook_permissions']))
        $this->facebook_permissions =  $conf['facebook_permissions'];

         $this->fb = new Facebook([
            'app_id' => $this->facebook_id, // Replace {app-id} with your app id
            'app_secret' => $this->facebook_secret,
            'default_graph_version' => 'v2.2',
        ]);

         $verifyLogin = $this->verifyLogin();

         if(isset($conf['debug']) && $conf['debug'] == true)
         {
             print '<pre>';
             print_r($verifyLogin);
             print '</pre>';
         }
    }

    public function getHtml()
    {
        // pega o link
        $link = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $helper = $this->fb->getRedirectLoginHelper();

        $permissions = $this->facebook_permissions; // Optional permissions
        $loginUrl = $helper->getLoginUrl($link, $permissions);

        echo str_replace('#link#', htmlspecialchars($loginUrl), $this->templateTag);
    }

    protected function verifyLogin()
    {
        $error = false;
        $tokenMetadata = false;
        $accessTokenShortLived = false;
        $accessTokenLongLived = false;

        $helper = $this->fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
            if(!is_null($accessToken))
            $accessTokenShortLived = @$accessToken->getValue();
        } catch(FacebookResponseException $e){
            // When Graph returns an error
            $error = 'Graph returned an error: ' . $e->getMessage();
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            $error = 'Facebook SDK returned an error: ' . $e->getMessage();
        }

        if (! isset($accessToken))
        {
            if ($helper->getError())
            {
                $error = "Error: " . $helper->getError() . "\n";
                $error .= "Error Code: " . $helper->getErrorCode() . "\n";
                $error .= "Error Reason: " . $helper->getErrorReason() . "\n";
                $error .= "Error Description: " . $helper->getErrorDescription() . "\n";
            }
            else
            {
                $error = 'Bad request';
            }

        }
        else
        {

            // The OAuth 2.0 client handler helps us manage access tokens
            $oAuth2Client = $this->fb->getOAuth2Client();

            // Get the access token metadata from /debug_token
            $tokenMetadata = $oAuth2Client->debugToken($accessToken);

            // Validation (these will throw FacebookSDKException's when they fail)
            $tokenMetadata->validateAppId($this->facebook_id); // Replace {app-id} with your app id
            // If you know the user ID this access token belongs to, you can validate it here
            //$tokenMetadata->validateUserId('123');
            $tokenMetadata->validateExpiration();

            if (! $accessToken->isLongLived()) {
                // Exchanges a short-lived access token for a long-lived one
                try {
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                } catch (FacebookSDKException $e) {
                    $error = "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                }

                $accessTokenLongLived = $accessToken->getValue();
            }

            //$_SESSION['fb_access_token'] = (string) $accessToken;
        }

        return [
            'error' => $error,
            'tokenMetadata' => $tokenMetadata,
            'accessToken' => $accessTokenShortLived,
            'accessTokenLongLived' => @$accessTokenLongLived
        ];
    }


}