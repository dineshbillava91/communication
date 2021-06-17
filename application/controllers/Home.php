<?php
/**
* Home is the default controller
*
* Home is a class that his used to register, normal login, social login
* profile update, change password
*
* @package Home
* @author Dinesh
* @version 1.0
* @access public
*/

class Home extends CI_Controller
{
    public function __construct()
    {
        parent:: __construct();
        $this->load->model('LoginModel');
        $this->load->model('ChatModel');

        $this->ClientId = "865598242952-dtqhd8iast5ep4dodjem763tcu0q46b1.apps.googleusercontent.com";
        $this->ClientSecret = "kAU2yWy0CZSX0wCIkJ-j7p3T";
        $this->RedirectURL = "http://localhost/communication/home/gmailAuthenticate";
        $this->ProfileURL = "home/profile";

        include_once APPPATH . "libraries/vendor/autoload.php";
    }

    /**
    * By default this function will be called
    *
    * @param string no parameters required
    * @return array it will load the landing page with signup, social login
    * @access public
    */
    public function index()
    {
        $google_client = new Google_Client();
        $google_client->setClientId($this->ClientId);
        $google_client->setClientSecret($this->ClientSecret);
        $google_client->setRedirectUri($this->RedirectURL);
        $google_client->addScope("email");
        $google_client->addScope("profile");
        $authUrl = $google_client->createAuthUrl();
        $data['authUrl'] = $authUrl;

        $this->load->view('home_view', $data);
    }

    /**
    * User login
    *
    * @param string no parameters required
    * @return array it will load the page with login & social login
    * @access public
    */
    public function login()
    {
        $google_client = new Google_Client();
        $google_client->setClientId($this->ClientId);
        $google_client->setClientSecret($this->ClientSecret);
        $google_client->setRedirectUri($this->RedirectURL);
        $google_client->addScope("email");
        $google_client->addScope("profile");
        $authUrl = $google_client->createAuthUrl();
        $data['authUrl'] = $authUrl;

        $this->load->view('login', $data);
    }

    /**
    * User registration
    *
    * @param string @firstname user first name
    * @param string @lastname user last name
    * @param string @email user email
    * @param string @password user password
    * @return array it will redirect to landing page
    * @access public
    */
    public function register()
    {
        $data['us_first_name'] = $_POST['firstname'];
        $data['us_last_name'] = $_POST['lastname'];
        $data['us_email'] = $_POST['email'];
        $data['us_password'] = $this->LoginModel->encryptPassword($_POST['password']);

        $this->LoginModel->register($data);

        redirect('home/login');
    }

    /**
    * User authentication
    *
    * @param string @email user emailid
    * @param string @password user password
    * @return array redirect to dashboard on successfull login
    * @access public
    */
    public function authenticate()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $status = $this->LoginModel->authenticate($email, $password);
        
        if ($status) {
            redirect('Chat');
        } else {
            $message = "Invalid Credentials !!!";
            $this->session->set_userdata('error', $message);

            $this->load->view('login');
        }
    }

    /**
    * Gmail authentication
    *
    * @param string no parameters
    * @return array redirect to dashboard on successfull login
    * @access public
    */
    public function gmailAuthenticate()
    {
        include_once APPPATH . "libraries/vendor/autoload.php";

        $google_client = new Google_Client();
        $google_client->setClientId("865598242952-dtqhd8iast5ep4dodjem763tcu0q46b1.apps.googleusercontent.com");
        $google_client->setClientSecret("kAU2yWy0CZSX0wCIkJ-j7p3T");
        $google_client->setRedirectUri($this->RedirectURL);
        $google_client->addScope("email");
        $google_client->addScope("profile");

        // Send Client Request
        $objOAuthService = new Google_Service_Oauth2($google_client);

        // Add Access Token to Session
        if (isset($_GET['code'])) {
            $google_client->authenticate($_GET['code']);
            $this->session->set_userdata('access_token', $google_client->getAccessToken());
            header('Location: ' . filter_var($this->RedirectURL, FILTER_SANITIZE_URL));
        }

        // Set Access Token to make Request
        if ($this->session->userdata('access_token')) {
            $google_client->setAccessToken($this->session->userdata('access_token'));
        }

        // Get User Data from Google and store them in $data
        if ($google_client->getAccessToken()) {
            $userData = $objOAuthService->userinfo->get();
            $data['us_first_name'] = $userData['givenName'];
            $data['us_last_name'] = $userData['familyName'];
            $data['us_email'] = $userData['email'];
            $data['us_social_login'] = 1;

            $this->LoginModel->socialLogin($data);
            redirect('chat');
        } else {
            $authUrl = $google_client->createAuthUrl();
            $data['authUrl'] = $authUrl;

            redirect('home');
        }
    }

    /**
    * Profile
    *
    * @param string no parameters
    * @return array it will load profile page
    * @access public
    */
    public function profile()
    {
        $email = $this->session->userdata('email');
        $data['userData'] = $this->ChatModel->getUserDetails($email);

        $this->load->view('profile', $data);
    }

    /**
    * Profile updation
    *
    * @param string @first_name user first name
    * @param string @last_name user last name
    * @param string @profile user image file
    * @return array it will redirect to profile page
    * @access public
    */
    public function updateProfile()
    {
        $user_id = $this->session->userdata('user_id');

        $data['us_first_name'] = $_POST['first_name'];
        $data['us_last_name'] = $_POST['last_name'];
        $data['us_modified_on'] = date('Y-m-d H:i:s');

        $this->LoginModel->updateProfile($data);

        if ($_FILES['profile']['name']) {
            $file_name = $user_id.".".pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);

            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']      = $file_name;
            $config['max_size']      = 2048;
            $this->load->library('upload', $config);

            if (! $this->upload->do_upload('profile')) {
                $error = array('error' => $this->upload->display_errors());
                $this->load->view('profile', $error);
            } else {
                $update_data['us_image'] = $file_name;
                $update_data['us_modified_on'] = date('Y-m-d H:i:s');
                $this->LoginModel->updateProfile($update_data);

                $this->session->set_userdata('user_image', $file_name);
                
                redirect($this->ProfileURL);
            }
        } else {
            redirect($this->ProfileURL);
        }
    }

    /**
    * Password change
    *
    * @param string @current_password user current password
    * @param string @password user new password
    * @return array it will redirect to profile page
    * @access public
    */
    public function updatePassword()
    {
        $this->LoginModel->updatePassword();
        redirect($this->ProfileURL);
    }

    /**
    * Logout will destroy the session
    *
    * @param string no parameters
    * @return array it will redirect to landing page
    * @access public
    */
    public function logout()
    {
        // Destroy entire session data
        $this->session->sess_destroy();

        redirect('home');
    }
}
