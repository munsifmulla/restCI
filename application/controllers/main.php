<?php session_start();
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');
class Main extends REST_Controller {

    public function access_get(){

        $userData = array(
            'accessToken'=> $this->get("access_token"),
            'userId'=> $this->get('user_id')
        );

        $setData = $this->session->set_userdata($userData);

//        if($this->session->userdata('userId')){
//            echo "Set<br/>";
//            echo $this->session->userdata('userId');
//            if($this->session->unset_userdata()){
//                echo "Session Now Destroyed";
//            }
//        }
//        else{
//            echo "Not Set <br/>";
//            $setData = $this->session->set_userdata($userData);
//        }


        if(($this->session)){
            echo "Session Saved : ".$this->session->userdata("accessToken");
        }
        else{
            echo "Some Error";
        }
    }

    public $accessToken;
    public $user_id;
    public function showData_get(){
        echo json_encode(array("token"=>$this->session->userdata("accessToken"),"user_id"=>$this->session->userdata("userId")));
        $this->accessToken = $this->session->userdata("accessToken");
        $this->user_id = $this->session->userdata("userId");
    }

    public function listJobs_get(){

        $base_cUrl = "https://api.springrole.com/beta/";
        $url = $base_cUrl."jobs?access_token=".$this->session->userdata("accessToken")."&user_id=".$this->session->userdata("userId");
//        $url = $base_cUrl."jobs?access_token=".$_SESSION['accessToken']."&user_id=".$_SESSION['userId'];

        $this->load->library('curl');
        $this->curl->create($url);
        $this->curl->option('returntransfer', 1);
        $this->curl->option('connecttimeout', 6000);
        $this->curl->option('SSL_VERIFYPEER', false); // For ssl site
        $this->curl->option('SSL_VERIFYHOST', false);
        $this->curl->option('SSLVERSION', 3); // end ssl
        $data = $this->curl->execute();
        echo $data;

    }

    public function searchHeaders_get(){
        $headers = getallheaders();

        if(!isset($headers['name'])){
            echo json_encode(array("error"=>"203","message"=> "Headers Required"));
        }
        else{
            echo "Welcome ".$headers['name'];
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */