<?php

class User {
    private $_db,
            $_data,
            $_sessionName,
            $_cookieName,
            $_isLoggenIn;
    
    
    public function __construct($user = null) {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName  = Config::get('remember/cookie_name');
        
        if(!$user)
        {
            if(Session::exists($this->_sessionName))
            {
                $user = Session::get($this->_sessionName);
                
                if($this->find($user))
                {
                    $this->_isLoggenIn = TRUE;
                } else {
                    // process logout
                }
                
            }
        } else {
            $this->find($user);
        }
    }
    
    public function update ($fields = array(), $id = NULL) {
        
        if(!$id && $this->isLoggedIn())
        {
            $id = $this->data()->id;
        }
        
        if(!$this->_db->update('users', $id, $fields)) {
            throw new Exception('There was a problem updating .');
        }
    }
    
    public function create($fields = array()) {
        // check if the creation of user has a problem.
        if($this->_db->insert('users', $fields))
        {
            if($this->_db->error() === TRUE)
            {
                throw new Exception('There was a problem creating an account');
            }
        }
    }
    
    public function find($user = NULL) {
        if($user){
            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->_db->get('users', array($field,'=',$user));
            
            if($data->count()){
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }
    
    public function login($username = NULL, $password = NULL, $remember = FALSE) {

        if (!$username && !$password && $this->exists()) {
            // Logs user in when the cookie hash value is matching the one in the database.
            // Logs user in
            Session::put($this->_sessionName, $this->data()->id);
        } else {

            $user = $this->find($username);

            if ($user) {
                if ($this->data()->password === Hash::make($password, $this->_data->salt)) {
                    Session::put($this->_sessionName, $this->data()->id);

                    if ($remember) {
                        $hash = Hash::unique();
                        $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));


                        if (!$hashCheck->count()) {
                            $this->_db->insert('users_session', array(
                                'user_id' => $this->data()->id,
                                'hash' => $hash
                            ));
                        } else {
                            $hash = $hashCheck->first()->hash;
                        }

                        Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                    }

                    return TRUE;
                }
            }
        }

        return false;
    }

    public function exists() {
        return (!empty($this->_data)) ? TRUE : FALSE ;
    }
    
    public function logout() {
        
        $this->_db->delete('users_session', array('user_id','=', $this->data()->id));
        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }
    
    public function data() {
        return $this->_data;
    }
    
    public function isLoggedIn(){
        return $this->_isLoggenIn;
    }
}

?>
