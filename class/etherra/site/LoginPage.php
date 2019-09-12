<?php
class site_LoginPage extends site_Page {
	var $username = 'admin';
	var $password = '';
    function __construct(){
        parent::__construct();
        $this->username = Config::get('site.username');
        $this->password = Config::get('site.password');
        //include(_CONFIG_ROOT.'login.php');
    }
    
    function showMainSpace(){
        if (Session::is_set('in')){
            $this->showLogoutForm();
        }
        else {
            $this->showLoginForm();
        }
    }
    
    function showLogoutForm(){
        if (isset($_POST['logout'])){
            Session::un_set('in');
            if (isset($_COOKIE['scuser'])){
            	setcookie("scuser","",time(),'/');
            	unset($_COOKIE['scuser']);
            }
            header('Location: /login.php');
            return;
        }
        print '<form method="POST" action="">
        <input type="submit" name="logout" value="Logout" />
        </form>
        ';
    }
    
    function showLoginForm(){
    	$url = isset($_POST['go'])?$_POST['go']:(isset($_GET['go'])?$_GET['go']:'/admin/');
        if (isset($_POST['username'])){
            if ($_POST['username']==$this->username && $_POST['password']==$this->password){
                Session::set('in', true);
                $this->createSecretCookie($this->username,$_POST['password']);
                header('Location: /');
                return;
            }
            else {
                print '�������� ��� ����������� � ������';
            }
        }
        print '<form method="POST" action="">
        <div>��� ������������:</div>
        <div>
        <input type="text" value="" name="username" />
        </div>
        <div>������:</div>
        <div>
        <input type="password" value="" name="password" />
        </div>
        <div>
        <input type="submit" name="login" value="����" />
        </div>
        </form>
        ';
    }
    
    function createSecretCookie($user_id,$password){
    	$time = time()+(3600*24*365); //one year
    	$data = base64_encode($user_id.'|'.md5($password));
    	setcookie("scuser",$data,$time,'/');
    }
    
}
?>