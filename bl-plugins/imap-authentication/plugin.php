<?php

class pluginImapAuthentication extends Plugin {

    const IMAP_SERVER_DB_FIELD = 'imapServer';
    const IMAP_ENCRYPTION_DB_FIELD = 'imapEncryption';

    public function init()
    {
        $this->dbFields = array(
            self::IMAP_SERVER_DB_FIELD=>'',
            self::IMAP_ENCRYPTION_DB_FIELD=>'ssl',
        );
    }

    public function form()
    {
        global $L;

        $html  = '<div class="alert alert-primary" role="alert">';
        $html .= $this->description();
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<label>'.$L->get('IMAP Server').'</label>';
        $html .= '<input name="imapServer" id="imapServer" type="text" value="'.$this->getValue(self::IMAP_SERVER_DB_FIELD).'">';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<label>'.$L->get('Encryption').'</label>';
        $html .= '<select name="imapEncryption" id="imapEncryption">';
        $html .= '<option value="ssl" '.('ssl' === $this->getValue(self::IMAP_ENCRYPTION_DB_FIELD) ? 'selected' : ''). '>SSL</option>';
        $html .= '<option value="tls" '.('tls' === $this->getValue(self::IMAP_ENCRYPTION_DB_FIELD) ? 'selected' : ''). '>TLS</option>';
        $html .= '</select>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Before validating user, check if exists.
     * If not and the user name is an email
     * and has an IMAP account create one.
     * @throws Exception
     */
    public function beforeVerifyUser()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (false === filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            new User($username);
        } catch (Exception $e) {
            $this->createUser($username, $password);
        }
    }

    /**
     * Validate user using IMAP
     * @throws Exception
     */
    public function afterVerifyUser()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!$this->authenticateUser($this->getValue(self::IMAP_SERVER_DB_FIELD), $this->getValue(self::IMAP_ENCRYPTION_DB_FIELD), $username, $password)) {
            return;
        }

        $user = new User($username);

        $loginClass = new login();
        $loginClass->setLogin($username, $user->role());
        Log::set(__METHOD__.LOG_SEP.'Successful user login using IMAP - Username ['.$username.']');
    }

    /**
     * @param $mailbox
     * @param $encryption
     * @param $username User's mail address
     * @param $password
     * @return bool
     */
    private function authenticateUser($mailbox, $encryption, $username, $password)
    {
        if (!function_exists('imap_open')) {
            Log::set(__METHOD__.LOG_SEP.'ERROR: PHP imap extension is not installed');
        }

        // Replace escaped @ symbol in uid (which is a mail address)
        // but only if there is no @ symbol and if there is a %40 inside the uid
        if (!(strpos($username, '@') !== false) && (strpos($username, '%40') !== false)) {
            $username = str_replace("%40","@",$username);
        }

        $imapConnection = @imap_open("{{$mailbox}/imap/{$encryption}}INBOX", $username, $password, OP_HALFOPEN, 1);
        $imapErrors = imap_errors();
        $imapAlerts = imap_alerts();
        if (!empty($imapErrors)) {
            Log::set(__METHOD__.LOG_SEP."IMAP Error:\n".print_r($imapErrors, true));
        }
        if (!empty($imapAlerts)) {
            Log::set(__METHOD__.LOG_SEP."WARNING: IMAP Warning:\n".print_r($imapAlerts, true));
        }
        if($imapConnection !== false) {
            imap_close($imapConnection);

            return true;
        }

        return false;
    }

    /**
     * @param $username User's mail address
     * @param $password
     * @throws Exception
     */
    private function createUser($username, $password)
    {
        global $users;

        if (!$this->authenticateUser($this->getValue(self::IMAP_SERVER_DB_FIELD), $this->getValue(self::IMAP_ENCRYPTION_DB_FIELD), $username, $password)) {
            return;
        }

        $usersClass = new Users();
        $usersClass->add(array(
            'username' => $username,
            'password' => '',
            'email' => $username,
            'nickname' => explode('@', $username)[0]
        ));

        $users = new Users();

        $user = new User($username);

        $loginClass = new Login();
        $loginClass->setLogin($username, $user->role());
        Log::set(__METHOD__.LOG_SEP.'Successful user creation using IMAP - Username ['.$username.']');
    }
}
