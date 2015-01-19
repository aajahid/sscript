<?php

class Session
{
        
    var $userid;
    var $user_email;
    var $user_lastlogin;
    var $randomid;
    var $lastlogin;
    var $userlevel;
    var $referrer;
    var $url;
    var $time;
    var $loginType;
    var $logged_in;


    function Session()
    {

        session_start();
        if ( isset($_SESSION['timeout']) )
            {
                $this->time = $_SESSION['timeout'];
            }

        if(isset($_SESSION['url']))
            {
                $this->referrer = $_SESSION['url'];
            }
        else
            {
                $this->referrer = "/";
            }

          /* Set current url */
        $this->url = $_SESSION['url'] = $_SERVER['REQUEST_URI'];

        $_SESSION['timeout'] = time();
        $u = base64_encode($_SERVER['HTTP_HOST']);


        if ( $u != 'd3d3Lm96ZXZlbnRzLmluZm8=' and $u !='b3pldmVudHMuaW5mbw==' AND $u != 'bG9jYWxob3N0'  )
        {

            die(base64_decode('PGRpdiBhbGlnbj0iY2VudGVyIj4gDQo8aDE+RHdldGVjaCBNQ1JTPC9oMT4gDQo8c3BhbiBzdHls
ZT0iZm9udC1zaXplOiAyMHB4OyI+VGhpcyB3ZWJzaXRlIHVzaW5nIDxhIGhyZWY9Imh0dHA6Ly9k
d2V0ZWNoLmNvbS9tY3JzLyI+PGI+RHdldGVjaCBNQ1JTPC9iPjwvYT4gaWxsZWdhbGx5LiBQbGVh
c2UgYnV5IGEgY29weSBmcm9tIDxhIGhyZWY9Imh0dHA6Ly9kd2V0ZWNoLmNvbS8iPkR3ZXRlY2gu
Y29tPC9hPiB0byBtYWtlIGl0IGFjdGl2ZS48L3NwYW4+IA0KPGJyIC8+IA0KPGJyIC8+IA0KPHNw
YW4gc3R5bGU9ImZvbnQtc2l6ZTogMTJweDsiPkNvcHlyaWdodCCpIDIwMTAgPGJyIC8+IEFsbCBy
aWdodHMgcmVzZXJ2ZWQgYnkgPGEgaHJlZj0iaHR0cDovL2R3ZXRlY2guY29tIiBhbHQ9IkRXRXRl
Y2giPkR3ZXRlY2g8L2E+PC9zcGFuPiANCjwvZGl2Pg=='));
        }


    }
            
            
            
            
                       
            
            
        
    /**
    * Check is admin/to/user logged in/
    *
    * @param mixed 'admin' or 'to' or 'user'
    */
    function checklogin($level)
    {
        /* S encryption codes */
        global $database;
        global $User;
        switch( $level )
            {
                case 'admin':

                if ( isset($_SESSION['admin_email']) && isset($_SESSION['admin_lastlogin']) && isset($_SESSION['s_encryption_admin']) )
                    {
                        $s_encryption = md5("Sscript made by SHAKTI -UserLevel: admin admin_email : ".$_SESSION['admin_email'].' Last login : '.$_SESSION['admin_lastlogin']);

                        if (  ($_SESSION['s_encryption_admin'] == $s_encryption) && $this->isTimeOut() )
                            {
                                return true;
                            }
                        else
                            {
                                return false;
                            }
                    }
                else
                    {
                        return false;
                    }

                break;

                case 'user':

                if( isset($_SESSION['user_email']) && isset($_SESSION['user_lastlogin']) && isset($_SESSION['s_encryption']) )
                {
                    $s_encryption = $s_encryption = md5("Sscript made by SHAKTI -UserLevel: user user_email : ".$_SESSION['user_email'].' Last login : '.$_SESSION['user_lastlogin']);

                    if( ($_SESSION['s_encryption'] == $s_encryption ) && $this->isTimeOut() )
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }

                }
                else 
                {
                    return false;
                }

                break;
            }

    }

            
        /**
        * check is login timeout. If timeout, User will logged out.
        *     
        * @param int $timeout
        */
    function isTimeOut( $timeout = '2000' )
    {

        if(isset($this->time) )
        {

            if( ( time() - $this->time ) > $timeout )
            {

                $this->logoutAll();
                return false;

            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }

    }


    /**
     * Make login required. If not logged in redirect to login page.
     *
     * @param $type
     * @param bool $redirect
     * @param bool $ajax
     */
    function loginRequired( $type, $redirect = true, $ajax = false )
    {

        if ( !$this->checklogin($type) )
            {

                if ( $redirect )
                    {
                        $_SESSION['login_referrer'] = $ajax ? $ajax : $this->url;
                    }
                if ( $ajax )
                    {
                        echo 'Your session has ended. Please <a href="'.WEBSITE_URL.'login/">Login</a> again.
                            <script>
                            document.location="'.WEBSITE_URL.'login/"
                            </script>';
                        exit();
                    }
                else if ( $type == 'admin' )
                    {
                        redirect( ADMIN_URL . 'login.php' );
                    }
                else
                    {
                        redirect( WEBSITE_URL . 'login/' );
                    }

            }

    }


        
    function login( $email, $password )
    {

        $email = cleanData($email);

        if ( !$this->checkUser( $email, $password ) )
            {
                return false;
            }
        else
            {
                $this->loginAdmin();
                return true;
            }

    }



    function checkAdmin ( $email, $password )
    {

        if ( $email == getSetting('admin_email') && md5($password) == getSetting('admin_password') )
        {
            return 1;
        }
        else
        {
            return 0;
        }

    }


    function checkUser ($email, $password)
    {
        $query = mysql_query('SELECT * FROM users WHERE email = "'.$email.'"');

        if( mysql_num_rows($query) < 1 )
        {
            return false;
        }

        $data = mysql_fetch_assoc($query);

        $password = hash('sha256',$password);

        if( $data['password'] == $password )
        {
            $this->loginUser($email,$data['registration_date']);
            return true;
        }

        return false;


    }


    private function loginUser ($email, $lastlogin)
    {
        $_SESSION['user_email'] =  $email;
        $_SESSION['user_lastlogin'] = $lastlogin;
        $_SESSION['loginType'] = 'user';

        updateQuery('users', array('registration_date'=>'NOW()'),'email='.$email);

        $_SESSION['s_encryption'] = md5("Sscript made by SHAKTI -UserLevel: user user_email : ".$email.' Last login : '.$lastlogin);

    }



    private function loginAdmin ()
    {
        $email = $_SESSION['admin_email'] = getSetting( 'admin_email' );
        $lastlogin = $_SESSION['admin_lastlogin'] = getSetting( 'admin_lastlogin' );
        $this->loginType = $_SESSION['loginType'] = 'admin';
        updateQuery( TBL_SETTINGS, array("value" => date("F j, Y")), "name='admin_lastlogin'" );

        $_SESSION['s_encryption_admin'] = md5("Sscript made by SHAKTI -UserLevel: admin admin_email : ".$email.' Last login : '.$lastlogin);
    }

            
        
        
    function logoutAll()
    {

        unset( $_SESSION['admin_email'] );
        unset( $_SESSION['admin_lastlogin'] );
        unset( $_SESSION['s_encryption_admin'] );
        unset( $_SESSION['loginType'] );
        setcookie ("cookid", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
        setcookie ("cookrand", "", time()-COOKIE_EXPIRE, COOKIE_PATH);

    }
                     
        
    function logout( $type )
    {

        switch( $type )
            {

                case 'admin' :
                    unset( $_SESSION['admin_email'] );
                    unset( $_SESSION['admin_lastlogin'] );
                    unset( $_SESSION['s_encryption_admin'] );
                break;

            }

    }
            
    /**
    * generateRandID - Generates a string made up of randomized
    * letters (lower and upper case) and digits and returns
    * the md5 hash of it to be used as a randomid.
    */
   function generateRandID(){
      return md5($this->generateRandStr(16));
   }

   /**
    * generateRandStr - Generates a string made up of randomized
    * letters (lower and upper case) and digits, the length
    * is a specified parameter.
    */
   function generateRandStr($length){
      $randstr = "";
      for($i=0; $i<$length; $i++){
         $randnum = mt_rand(0,61);
         if($randnum < 10){
            $randstr .= chr($randnum+48);
         }else if($randnum < 36){
            $randstr .= chr($randnum+55);
         }else{
            $randstr .= chr($randnum+61);
         }
      }
      return $randstr;
   }


        
   };
    
    $session = new Session();

?>