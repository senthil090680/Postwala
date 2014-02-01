<?php

/////////////////////class account


class Account {
    public $id = null;
    public $name = null;
    public $email = null;
    public $location = null;
    public $active = 0;
    public $exists = false;
    public $status_password = false;

    //constructor
    function __construct($email){
        
        
        if ($email != "")
        {
            $ocdb=phpMyDB::GetInstance();

                    $query = "SELECT t1.USERID, t2.NAME, t1.ISVALID
                              FROM   classifieds_visitor t1
                                   , classifieds_visitorinfo t2
                              WHERE t1.USERID = t2.USERID
                              AND   t1.email = '".$email."'
                              LIMIT 1";

                    $result=$ocdb->query($query);

                    if (mysql_num_rows($result))
                    {
                        $this->email = $email;

                        $row=mysql_fetch_assoc($result);
                        $this->id = $row['USERID'];
                        $this->name = $row['NAME'];

                        if($row['ISVALID'] == "Y") $this->active = 1;
                        else $this->active = 0;

                        $this->exists = true;
                        

                    }
                    else
                       $this->exists = false;
        }
        else
            $this->exists = false;


    }

    public static function createById($id){ //construct by id
        $account = new Account("");

        if (is_numeric($id))
        {
            $ocdb=phpMyDB::GetInstance();

            $query = "SELECT t1.EMAIL, t1.USERID, t2.NAME, 0 AS idLocation, t1.ISVALID
                      FROM   classifieds_visitor t1
                           , classifieds_visitorinfo t2
                      WHERE t1.USERID = t2.USERID
                      AND   t1.USERID  = ".$id."
                      LIMIT 1";

             $result=$ocdb->query($query);

             if (mysql_num_rows($result))
             {
                 $row=mysql_fetch_assoc($result);

                 $account->id = $id;
                 $account->name = $row['NAME'];
                 $account->email = $row["EMAIL"];
                 $account->location = $row['idLocation'];

                 if($row['ISVALID'] == "Y") $account->active = 1;
                 else $account->active = 0;

                 $account->exists = true;

            } else $account->exists = false;

        } else $account->exists = false;

        return $account;
    }

    public static function createBySession(){ //construct by session
        $account = new Account("");

        $id = $_SESSION["ocAccount"];
        if (is_numeric($id))
        {
            return self::createById($id);
        }
        else $account->exists = false;

        return $account;
    }

    //Register new account
        public function Register($name,$email,$password)
        {
           if (!$this->exists)
           {
            $ocdb=phpMyDB::GetInstance();

            $token = $this->generateActivationToken();

            $maxid = 0;
            $query = "SELECT MAX(USERID) as MAX_USERID  FROM classifieds_visitor" ;
            $result=$ocdb->query($query);
            if (mysql_num_rows($result))
            {
                $row=mysql_fetch_assoc($result);
                $maxid = $row['MAX_USERID'] + 1;
            }


            $this->id = $maxid ;
            $this->name = $name;
            $this->email = $email;
            $this->active = 0;
            $this->exists = true;

            $ip = $_SERVER['REMOTE_ADDR'];


            $sql   = " INSERT INTO classifieds_visitor (USERID
                                                        , EMAIL
                                                        , PASSWORD
                                                        , LASTLOGIN
                                                        , ISVALID
                                                        , VERIFICATION_CODE
                                                        , REGISTERATION_DATE
                                                        , REGISTERATION_IP) VALUES  ("
                                                          .$maxid.
                                                        ", '". $email ."'
                                                        , '".  $password ."'
                                                        , CURRENT_TIMESTAMP
                                                        , 'N'
                                                        , '". $token ."'
                                                        , CURRENT_TIMESTAMP
                                                        , '". $ip  ."' )"  ;

             $ocdb->query($sql);

             $sql   = " INSERT INTO classifieds_visitorinfo (USERID , NAME ) VALUES (" . $maxid  .", '". $name ."')"  ;

             $ocdb->query($sql);

             return true;

           } else return false;
        }

    //Activate account by token
        public function Activate($token)
        {
            if ($this->exists)
            {
                    $ocdb=phpMyDB::GetInstance();

                    $query = "SELECT USERID FROM classifieds_visitor
                    WHERE
                    (VERIFICATION_CODE = '".$token."') AND (EMAIL = '".$this->email."')";

                    $result=$ocdb->query($query);
                    if (mysql_num_rows($result))
                    {
                        $query = "UPDATE classifieds_visitor
                                  SET ISVALID = 'Y' , VERIFICATION_CODE = '' 
                                  WHERE
                                  USERID = ".$this->id."";

                    $ocdb->query($query);

                return true;
            } else return false;
        } else return false;
    }

    //Logon
    function logOn($password,$remember=false,$rememberCookie="")
    {
        $ocdb=phpMyDB::GetInstance();


		$query = "SELECT PASSWORD, ISVALID FROM classifieds_visitor
		WHERE
		email = '".$this->email."'
		LIMIT 1";
                
                	
		$result=$ocdb->query($query);
		if (mysql_num_rows($result))
		{
			 $row=mysql_fetch_assoc($result);

			 $this->exists = true;

                         
			 if ($row["PASSWORD"]==$password)
			 {
				  $this->status_password = true;

				  if ($row["ISVALID"]=='Y')
				  {
					  $_SESSION["ocAccount"] = $this->id;
					  if ($remember)
					  {
						  if ($rememberCookie!="")
						  {
							 $expire=time()+60*60*24*30;
							 setcookie($rememberCookie, $this->email, $expire);
						  }
					  }
					  else if ($rememberCookie!="") setcookie($rememberCookie, "", time()-3600);

					  $this->active = 1;

					  //update lastSigninDate
					  $query = "UPDATE classifieds_visitor
								SET LASTLOGIN = CURRENT_TIMESTAMP()
								WHERE
								USERID = ".$this->id."";
					  $ocdb->query($query);

					  return true;
				  }
				  else
				  {
					  $this->active = 0;
					  return false;
				  }
			  }
			  else
			  {
				  $this->status_password = false;
				  return false;
			  }
		   }
		   else
		   {
			   $this->exists = false;
			   return false;
		   }
	 }

    //Logout
    public static function logOut()
    {
        if(isset($_SESSION["ocAccount"]))
        {
              $_SESSION["ocAccount"] = null;

              unset($_SESSION["ocAccount"]);
        }
    }

    //Return account's activation token
    public function token()
    {
                $ocdb=phpMyDB::GetInstance();

                $query = "SELECT
                                VERIFICATION_CODE
                                FROM
                                classifieds_visitor
                                WHERE
                                USERID = ".$this->id."";

        $result=$ocdb->query($query);

        if (mysql_num_rows($result))
        {
            $row=mysql_fetch_assoc($result);
            $token = $row['VERIFICATION_CODE'];

            return $token;
        } else return null;
    }

    //Return the timestamp when the account was registered
    public function signupTimeStamp()
    {
        $ocdb=phpMyDB::GetInstance();

        $query = "SELECT REGISTERATION_DATE
                  FROM
                  classifieds_visitor
                  WHERE
                  USERID = ".$this->id."";

        $result=$ocdb->query($query);

        if (mysql_num_rows($result))
        {
             $row=mysql_fetch_assoc($result);

             return ($row['REGISTERATION_DATE']);
        }
        else return null;
    }

    //Return account's passsword
    public function password()
    {
                $ocdb=phpMyDB::GetInstance();

                $query = "SELECT PASSWORD FROM
                                classifieds_visitor
                                WHERE
                                USERID = ".$this->id."";

        $result=$ocdb->query($query);

        if (mysql_num_rows($result))
        {
            $row=mysql_fetch_assoc($result);

            return $row['PASSWORD'];
        }
        else return null;
    }

    //Update an account's email
    public function updateName($name)
    {
                $ocdb=phpMyDB::GetInstance();

                $query = "UPDATE classifieds_visitorinfo
                           SET name = '".$name."'
                           WHERE
                           USERID = ".$this->id."";

                $this->name = $name;

                return $ocdb->query($query);
     }

    //Update an account's password
     public function updatePassword($password)
     {
            $ocdb=phpMyDB::GetInstance();

            $ip = $_SERVER['REMOTE_ADDR'];
            $sql   = " UPDATE classifieds_visitor SET PASSWORD = '".  $password ."' ,
                       REGISTERATION_IP = '". $ip  ."'  WHERE USERID = ".$this->id."";

            $ocdb->query($sql);

            return $ocdb->query($sql);
        }

    //Helper functions

    //Function lostpass var if set will check for an active account.
    private function validateActivationToken($token,$lostpass=null)
    {
		$ocdb=phpMyDB::GetInstance();

		if($lostpass == null)
		{
				$query = "SELECT VERIFICATION_CODE
							FROM classifieds_visitor
							WHERE ISVALID = 'N'
							AND
							VERIFICATION_CODE ='".trim($token)."'
							LIMIT 1";
		}
		else
		{
				 $query = "SELECT VERIFICATION_CODE
							FROM classifieds_visitor
							WHERE ISVALID = 'Y'
							AND
							VERIFICATION_CODE ='".trim($token)."'
							LIMIT 1";
		}

		$result=$ocdb->query($query);

		if (mysql_num_rows($result)) return true;
		else return false;
    }

    //Generate an activation key
    private function generateActivationToken()
    {
		$gen;

		do
		{
				$gen = md5(uniqid(mt_rand(), false));
		}
		while($this->validateActivationToken($gen));

		return $gen;
    }
}

?>