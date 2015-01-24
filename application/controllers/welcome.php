<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
    public function listJobs(){

        $base_cUrl = "https://api.springrole.com/beta/";
        $url = $base_cUrl."jobs?access_token=".$this->session->userdata("accessToken")."&user_id=".$this->session->userdata("userId");

        // Initialize session and set URL.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);

    }
    public function myRecomendations(){

        $base_cUrl = "https://api.springrole.com/beta/";
        $url = $base_cUrl."recommendations/me?access_token=".$this->session->userdata("accessToken")."&user_id=".$this->session->userdata("userId");

        // Initialize session and set URL.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);

    }
    public function mySkills(){

        $base_cUrl = "https://api.springrole.com/beta/";
        $url = $base_cUrl."me/skills?access_token=".$this->session->userdata("accessToken")."&user_id=".$this->session->userdata("userId");

        // Initialize session and set URL.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);

    }

    public function myReferrals(){

        $base_cUrl = "https://api.springrole.com/beta/";
        $url = $base_cUrl."me/referrals?access_token=".$this->session->userdata("accessToken")."&user_id=".$this->session->userdata("userId");

        // Initialize session and set URL.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);

    }
    public function getImages(){
        $start = 1;
        $responsArray= array();$flag=false;
        for($start<1; $start<51; $start++){

        $url = 'https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=fuzzy%20monkey&imgsz=small&rsz=8&start='.$start;

        // Initialize session and set URL.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $newres = json_decode($response, true);
//        echo $response;

            if(sizeof($newres['responseData']['results']) > 1){
                foreach($newres['responseData']['results'] as $item){
                    if(!in_array($item['url'], $responsArray)){
                        array_push($responsArray,$item['url']);
                    }
                }
            }
            else{
                array_push($responsArray, $newres['responseData']['results']['url']);
            }

            if($start == 50){
                $flag=true;
            }
            else{
                $flag = false;
            }
        curl_close($ch);
        }
        if($flag == true){
            foreach($responsArray as $item)
            echo "<img src = '".$item."' width = '80px'/> <hr/>";
        }
    }

    //Use this to, populate friends you can refer
    public function myPotentialReferrals(){

        $base_cUrl = "https://api.springrole.com/beta/";
        $url = $base_cUrl."me/referrals/potential?access_token=".$this->session->userdata("accessToken")."&user_id=".$this->session->userdata("userId")."&page_size=50";

        // Initialize session and set URL.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $newRes = json_decode($response, true);

//        print_r($newRes);

        foreach($newRes['data'] as $title){
            foreach($title as $key=>$value){
                echo $key." -> ".$value."<br/>\n";
            }
            echo "<hr/>";
        }
        curl_close($ch);

    }

    public function referAfriend(){
        $base_cUrl = "https://api.springrole.com/beta/";
        $url = $base_cUrl."recommendations/referrals";
        $data = json_encode(array(
            "access_token:".$this->session->userdata("accessToken"),
            "user_id:".$this->session->userdata("userId"),
            "link:".$this->input->post("link")
        ));
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    }

    public function recomendation(){

        $base_cUrl = "https://api.springrole.com/beta/";
//        $url = $base_cUrl."jobs?access_token=".$this->session->userdata("accessToken")."&user_id=".$this->session->userdata("userId");
        $url = $base_cUrl."recommendations";

        $data = json_encode(array(
            "access_token"=>$this->session->userdata("accessToken"),
            "user_id"=>$this->session->userdata("userId"),
            "friends"=>array(
                array(
                    "name"=>"Siddesh",
                    "email"=>"sidgad777@gmail.com"
                )
            )
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:application/json"
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);

    }

    public function testHeaders(){

        $base_cUrl = "https://api.springrole.com/beta/";
//        $url = $base_cUrl."jobs?access_token=".$this->session->userdata("accessToken")."&user_id=".$this->session->userdata("userId");

        $url = "http://localhost.home.com/CIServer/swipIn/users";
//        $url = "http://fdrive.in/ci/swipIn/users";
        // Initialize session and set URL.
        $data = json_encode(
            array("cat_one"=>"Tiger","cat_two"=>"Adult Lion")
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-type:application/json",
            "Accept:application/json",
            "access_token:sample"
            )
        );
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        echo $response;
        curl_close($ch);

    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */