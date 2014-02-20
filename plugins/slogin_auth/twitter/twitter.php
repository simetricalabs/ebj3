<?php
/**
 * Social Login
 *
 * @version 	1.0
 * @author		Arkadiy, Joomline
 * @copyright	© 2012. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_BASE.'/plugins/slogin_auth/twitter/assets/twitteroauth/twitteroauth.php';

class plgSlogin_authTwitter extends JPlugin
{
    public function onSloginAuth()
    {
        $twitauth = new SloginTwitterOAuth($this->params->get('id'), $this->params->get('password'));

        $request_token = $twitauth->getRequestToken('');

        if (empty($request_token)) {
            die('Error: oauth_token not set');
        }

        //установка значений в сессию
        $session = JFactory::getSession();
        $session->set('oauth_token', $request_token['oauth_token']);
        $session->set('oauth_token_secret', $request_token['oauth_token_secret']);

        //редирект на страницу авторизации
        $url = $twitauth->getAuthorizeURL($request_token);

        return $url;
    }

    public function onSloginCheck()
    {
        $input = JFactory::getApplication()->input;

        $request = null;

        $code = $input->getString('oauth_verifier', '');

        $returnRequest = new SloginRequest();

        if ($code) {

            //получение значений из сессии
            $session = JFactory::getSession();
            $oauth_token = $session->get('oauth_token');
            $oauth_token_secret = $session->get('oauth_token_secret');

            $connection = new SloginTwitterOAuth($this->params->get('id'), $this->params->get('password'), $oauth_token, $oauth_token_secret);
            $access_token = $connection->getAccessToken($code);
            /* $access_token
            array(4) {
                ["oauth_token"]=> string(50) "831353905-gNje5thq0Mbdped9KMwIEQSzMLHvVntOyOZQndYj"
                ["oauth_token_secret"]=> string(43) "mnTSrmPh6lO0mLGfaUnqf3Ct7QVe4GdaIq4eQLmlE04"
                ["user_id"]=> string(9) "831353905"
                ["screen_name"]=> string(10) "ArkadiySed"
            }
            */

            if (200 == $connection->http_code) {
                $request = $connection->get('users/show', array('screen_name' => $access_token['screen_name']));;
                //удаляем данные из сессии, уже не нужны
                $session->clear('oauth_token');
                $session->clear('oauth_signature');

                if(empty($request)){
                    echo 'Error - empty user data';
                    exit;
                }
                else if(!empty($request->errors)){
                    foreach($request->errors as $errors){
                        echo 'Error - '. $errors->message;
                    }
                    exit;
                }

                $returnRequest->first_name  = $request->name;
                $returnRequest->last_name   = $request->screen_name;
                $returnRequest->id          = $request->id;
                $returnRequest->real_name   = $request->name.' '.$request->screen_name;
                $returnRequest->display_name = $request->screen_name;
                $returnRequest->all_request  = $request;
                return $returnRequest;
            }
            else{
                echo 'Error - not connect to Twitter';
                exit;
            }
        }
        else{
            echo 'Error - empty code';
            exit;
        }
    }

    public function onCreateSloginLink(&$links, $add = '')
    {
        $i = count($links);
        $links[$i]['link'] = 'index.php?option=com_slogin&task=auth&plugin=twitter' . $add;
        $links[$i]['class'] = 'twitterslogin';
        $links[$i]['plugin_name'] = 'twitter';
    }
}
