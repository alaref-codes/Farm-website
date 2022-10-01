<?php

    class BlogMember extends BlogReader{
        
        private $username;
        private $farmname;

        
        public function __construct($pUsername,$pFarmname){
            parent::__construct();
            $this->username = $pUsername;
            $this->farmname = $pFarmname;

            $this->type = BlogMember::MEMBER;
        }
        
        public function isDuplicateID(){
            
            $sql = "SELECT count(username) AS num FROM users WHERE username = :username";
            
            $values = array(
                array(':username', $this->username)
            );
        
            $result = $this->db->queryDB($sql, Database::SELECTSINGLE, $values);

            if ($result['num'] == 0)
                return false;
            else
                return true;            
            
        }
        
        public function insertIntoFarmDB($pPassword){
            
            // Inserting into the farm table
            $sql = "INSERT INTO farm (name) VALUES (:farmname)";
            $values = array(
                array(':farmname', $this->farmname)
            );

            $this->db->queryDB($sql, Database::EXECUTE, $values);


            // Getting the new farm id
            $sql = "SELECT id FROM farm WHERE name = :farmname";
            
            $values = array(
                array(':farmname', $this->farmname)
            );
        
            $result = $this->db->queryDB($sql, Database::SELECTSINGLE, $values);
            $this->insertIntoUsersDB($pPassword, $result['id']);
        }

        public function insertIntoUsersDB($pPassword, $farmid) {

            
            $sql = "INSERT INTO users (username, password, farm_id) VALUES (:username, :password, :farmid)";
            
            $values = array(
                array(':username', $this->username),
                array(':password', password_hash($pPassword, PASSWORD_DEFAULT)),
                array(':farmid', $farmid),

            );

            $this->db->queryDB($sql, Database::EXECUTE, $values);

        }
        
        public function isValidLogin($pPassword){
            $sql = "SELECT password FROM users WHERE username = :username";
            
            $values = array(
                array(':username', $this->username)
            );

            $result = $this->db->queryDB($sql, Database::SELECTSINGLE, $values);
            
            if (isset($result['password']) && password_verify($pPassword, $result['password']))
                return true;
            else
                return false;

        }
        
        private function getLatestPostID(){
            $sql = "SELECT max(id) AS max FROM posts";
            
            $result = $this->db->queryDB($sql, Database::SELECTSINGLE);
            
            if (isset($result['max']))
                return $result['max'];
            else
                return 0;
            
        }
        
        public function updateLastViewedPost(){
            $max = $this->getLatestPostID();
            
            $sql = "UPDATE users SET last_viewed = :max WHERE username = :username";
            
            $values = array(
                array(':max', $max),
                array(':username', $this->username)
            );

            $this->db->queryDB($sql, Database::EXECUTE, $values);
            
        }
        
        public function getLastViewedPost(){
            $sql = "SELECT last_viewed FROM users WHERE username = :username";

            $values = array(
                array(':username', $this->username)
            );

            $result = $this->db->queryDB($sql, Database::SELECTSINGLE, $values);
            
            if (isset($result['last_viewed']))
                return $result['last_viewed'];
            else
                return 0;
            
        }
    }




