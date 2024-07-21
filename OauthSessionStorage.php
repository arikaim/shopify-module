<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c) Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Shopify;

use Shopify\Auth\SessionStorage;
use Shopify\Auth\Session;
use Shopify\Auth\AccessTokenOnlineUserInfo;
use Arikaim\Core\Db\Model;

/**
 *  Shopify Oauth session handler
 */
class OauthSessionStorage implements SessionStorage
{
    /**
     * Db model ref
     * @var object
     */
    protected $model;

    /**
     *  Constructor
     */
    public function __construct()
    {
        $this->model = Model::OauthTokens('oauth');
    }

    /**
     * Loads the Session object from oauth db model
     *
     * @param string $sessionId Id of the Session 
     * @return Session Returns Session or null
     */
    public function loadSession(string $sessionId): ?Session
    {
        $token = $this->model->sessionIdQuery($sessionId)->first();
        if ($token == null) {
            return null;
        }

        $session = new Session(
            $token->session_id, 
            $token->getOption('shop'), 
            $token->getOption('isOnline'),
            $token->getOption('state')
        );

        $session->setScope($token->scopes);
        $session->setExpires($token->date_expired);
        $session->setAccessToken($token->access_token);

        // get user
        $user = $token->getOption('user',null);
        if (\is_array($user) == true) {
            $session->setOnlineAccessInfo(new AccessTokenOnlineUserInfo(
                $user['id'],
                $user['firstName'],
                $user['lastName'],
                $user['email'],
                $user['emailVerified'],
                $user['accountOwner'],
                $user['locale'],
                $user['collaborator'],
            ));
        }
     
        return $session;
    }

    /**
     * Stores session into db model
     *
     * @param Session $session 
     * @return bool 
     */
    public function storeSession(Session $session): bool
    {
        $user = $session->getOnlineAccessInfo();
        $userInfo = ($user !== null) ? null :
            [
                'id'            => $user->getId(),
                'firstName'     => $user->getFirstName(),
                'lastName'      => $user->getLastName(),
                'email'         => $user->getEmail(),
                'emailVerified' => $user->isEmailVerified(),
                'accountOwner'  => $user->isAccountOwner(),
                'locale'        => $user->getLocale(),
                'collaborator'  => $user->isCollaborator()
            ];
       
        $data = [
            'session_id'   => $session->getId(),
            'access_token' => $session->getAccessToken(),
            'type'         => 2,
            'scopes'       => $session->getScope(),
            'driver'       => 'shopify.api'
        ];

        $token = $this->model->sessionIdQuery($session->getId())->first();
        
        if ($token == null) {
            $token = $this->model->create($data);
            if ($token == null) {
                return false;
            }
        } else {
            if ($token->update($data) === false) {
                return false;
            }
        }
           
        $token->saveOptions([
            'shop'     => $session->getShop(),
            'isOnline' => $session->isOnline(),
            'state'    => $session->getState(),
            'user'     => $userInfo
        ]);

        return true;
    }

    /**
     * Deletes a Session
     *
     * @param string $sessionId 
     * @return bool 
     */
    public function deleteSession(string $sessionId): bool
    {
        $token = $this->model->sessionIdQuery($sessionId)->first();
        
        return ($token == null) ? true : ($token->delete() !== false);
    }
}
