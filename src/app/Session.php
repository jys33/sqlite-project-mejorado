<?php

class Session
{
    /**
     *
     * @var string Web user's name
     */
    public $username = null;

    /**
     *
     * @var string CSRF token for HTML forms
     */
    public $csrftoken = null;

    /**
     * Simple authentication of the web end-user
     *
     * @param string $username
     * @return boolean True if the user is allowed to use the application
     */
    public function authenticateUser($username)
    {
        switch ($username) {
            case 'admin':
            case 'simon':
                $this->username = $username;
                return (true);  // OK to login
            default:
                $this->username = null;
                return (false); // Not OK
        }
    }

    /**
     * Check if the current user is allowed to do administrator tasks
     *
     * @return boolean
     */
    public function isPrivilegedUser()
    {
        if ($this->username === 'admin')
            return (true);
        else
            return (false);
    }

    /**
     * Store the session data to provide a stateful web experience
     */
    public function setSession()
    {
        $_SESSION['username']    = $this->username;
        $_SESSION['csrftoken']   = $this->csrftoken;
    }

    /**
     * Get the session data to provide a stateful web experience
     */
    public function getSession()
    {
        $this->username = $_SESSION['username'] ?? null;
        $this->csrftoken = $_SESSION['csrftoken'] ?? null;
    }

    /**
     * Logout the current user
     */
    public function clearSession()
    {
        $_SESSION = [];
        $this->username = null;
        $this->csrftoken = null;
    }

    /**
     * Records a token to check that any submitted form was generated
     * by the application.
     *
     * For real systems the CSRF token should be securely,
     * randomly generated so it cannot be guessed by a hacker
     * mt_rand() is not sufficient for production systems.
     */
    public function setCsrfToken()
    {
        $this->csrftoken = mt_rand();
        $this->setSession();
    }
}
