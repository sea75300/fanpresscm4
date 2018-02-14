<?php
    /**
     * Login controller
     * @author Stefan Seehafer <sea75300@yahoo.de>
     * @copyright (c) 2011-2018, Stefan Seehafer
     * @license http://www.gnu.org/licenses/gpl.txt GPLv3
     */
    namespace fpcm\controller\action\system;
    
    class login extends \fpcm\controller\abstracts\controller {
        
        /**
         * aktuelle Anzahl an Fehler-Logins
         * @var int
         */
        protected $currentAttempts   = 0;
        
        /**
         * ist Login gespeerrt
         * @var bool
         */
        protected $loginLocked       = false;
        
        /**
         * wann wurde Login gesperrt
         * @var int
         */
        protected $loginLockedDate   = 0;
        
        /**
         * Sperrzeit
         * @var int
         */
        protected $loginLockedExpire = 600;
        
        /**
         * Page Token Prüfung erfolgreich
         * @var bool
         */
        protected $pageTokenOk = true;

        /**
         * Konstruktor
         */
        public function __construct()
        {
            parent::__construct();
            $this->loginLockedExpire = session_cache_expire();
        }

        /**
         * 
         * @return string
         */
        protected function getViewPath()
        {
            return 'login/login';
        }

        /**
         * Request-Handler
         * @return boolean
         */
        public function request()
        {
            if ($this->session->exists()) {
                return $this->redirect('system/dashboard');
            }
            session_start();
            
            $this->pageTokenOk = $this->checkPageToken();
            $this->loginLocked();

            if ($this->buttonClicked('login') && !is_null($this->getRequestVar('login')) && !$this->loginLocked && $this->pageTokenOk) {
                $data = $this->getRequestVar('login');                
                $data = $this->events->runEvent('loginBefore', $data);
                
                $session = new \fpcm\model\system\session();
                $loginRes = $session->checkUser($data['username'], $data['password']);

                if ($loginRes === \fpcm\model\users\author::AUTHOR_ERROR_DISABLED) {
                    $this->currentAttempts = $this->config->system_loginfailed_locked;
                    $this->view->addErrorMessage('LOGIN_FAILED_DISABLED');
                    if ($this->currentAttempts == $this->config->system_loginfailed_locked) {
                        $this->loginLocked();
                    }
                } elseif ($loginRes === true && $session->save() && $session->setCookie()) {
                    session_destroy();
                    $this->redirect('system/dashboard');
                } else {
                    $this->currentAttempts++;
                    \fpcm\classes\http::setSessionVar('loginAttempts', $this->currentAttempts);
                    $this->view->addErrorMessage('LOGIN_FAILED');
                    if ($this->currentAttempts == $this->config->system_loginfailed_locked) {
                        $this->loginLocked();
                    }
                }                
            }
            
            if ($this->buttonClicked('reset') && !is_null($this->getRequestVar('username')) && !is_null($this->getRequestVar('email')) && !$this->loginLocked && $this->pageTokenOk) {

                $userList = new \fpcm\model\users\userList();
                $id = $userList->getUserIdByUsername($this->getRequestVar('username'));
                
                if (!$id) $this->redirect();
                
                $user = new \fpcm\model\users\author($id);
                
                if ($user->getEmail() == $this->getRequestVar('email') && $user->resetPassword()) {
                    $this->view->addNoticeMessage('LOGIN_PASSWORD_RESET');
                } else {
                    fpcmLogSystem("Passwort reset for user id {$user->getUsername()} failed.");
                    $this->view->addErrorMessage('LOGIN_PASSWORD_RESET_FAILED');
                }
                
            }
            
            if (!is_null($this->getRequestVar('nologin'))) {
                $this->view->addErrorMessage('LOGIN_REQUIRED');
            }            
            
            $reset  = !is_null($this->getRequestVar('reset')) ? true : false;
            
            $this->view->assign('userNameField', $reset ? 'username' : 'login[username]'); 
            $this->view->assign('resetPasswort', $reset);
            $this->view->assign('noFullWrapper', true);

            return true;
            
        }

        /**
         * 
         * @return boolean
         */
        public function hasAccess()
        {
            return true;
        }
        
        /**
         * Controller-Processing
         */
        public function process()
        {            
            if (!$this->pageTokenOk && ($this->buttonClicked('reset') || $this->buttonClicked('login'))) {
                $this->view->addErrorMessage('CSRF_INVALID');
            }

            if (($this->ipList->ipIsLocked() || $this->ipList->ipIsLocked('nologin'))) {
                $this->view->addErrorMessage('ERROR_IP_LOCKED');
                $this->view->assign('lockedGlobal', true);
            }            
            
            if ($this->loginLocked) {
                $this->view->addErrorMessage('LOGIN_ATTEMPTS_MAX', array(
                    '{{logincount}}' => $this->currentAttempts,
                    '{{lockedtime}}' => $this->loginLockedExpire / 60,
                    '{{lockeddate}}' => date($this->config->system_dtmask, $this->loginLockedDate)
                ));
            }
            
            $this->view->addJsFiles(['login.js']);

            $this->view->assign('loginAttempts', $this->currentAttempts);
            $this->view->assign('loginAttemptsMax', $this->config->system_loginfailed_locked);
            $this->view->render();
        }
        
        /**
         * Prüft, ob Login gesperrt ist
         */
        protected function loginLocked()
        {
            if (!\fpcm\classes\http::getSessionVar('loginAttempts')) {
                \fpcm\classes\http::setSessionVar('loginAttempts', $this->currentAttempts);
            } else {                
                $this->currentAttempts = \fpcm\classes\http::getSessionVar('loginAttempts');
            }

            if (\fpcm\classes\http::getSessionVar('lockedTime')) {                
                $this->loginLockedDate  = \fpcm\classes\http::getSessionVar('lockedTime');
            }
            
            if ($this->currentAttempts >= $this->config->system_loginfailed_locked) {
                $this->loginLocked      = true;
                
                if (!$this->loginLockedDate) {
                    $this->loginLockedDate  = time();
                    \fpcm\classes\http::setSessionVar('lockedTime', $this->loginLockedDate);                    
                }
            }

            if ($this->loginLocked && $this->loginLockedDate + $this->loginLockedExpire <= time()) {
                $this->loginLocked      = false;
                $this->loginLockedDate  = 0;
                $this->currentAttempts  = 0;
                
                session_destroy();
            }            
        }

    }
?>
