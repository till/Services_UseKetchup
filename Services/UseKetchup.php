<?php
/**
 * @ignore
 */
require_once 'HTTP/Request2.php';

require_once 'Services/UseKetchup/Common.php';

class Services_UseKetchup extends Services_UseKetchup_Common
{
    protected $password;
    protected $username;

    protected $apiToken;

    protected $endpoint = 'http://useketchup.com/api/v1';

    protected $subs;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->getApiToken();
    }

    public function __get($sub)
    {
        $sub = ucwords(strtolower($sub));
        switch ($sub) {
        case 'Items':
        case 'Meetings':
        case 'Notes':
        case 'Projects':
        case 'User':

            if (!isset($this->subs[$sub])) {

                $className = "Services_UseKetchup_{$sub}";


                if (!class_exists($className)) {
                    $fileName  = str_replace('_', '/', $className) . '.php';

                    if (!(include $fileName)) {
                        throw new RuntimeException("Cannot find: $fileName.");
                    }
                }

                $this->subs[$sub] = new $className;
                $this->subs[$sub]
                    ->setApiToken($this->apiToken)
                    ->setPassword($this->password)
                    ->setUsername($this->username);
            }
            return $this->subs[$sub];

            break;

        default:
            throw new LogicException("Unknown $sub. Maybe not implemented?");
        }
    }

    public function getApiToken()
    {
        if ($this->apiToken !== null) {
            return $this->apiToken;
        }
        $resp = $this->makeRequest('/profile.json');
        $data = $this->parseResponse($resp);

        $this->apiToken = $data->single_access_token;

        return $this->apiToken;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
}