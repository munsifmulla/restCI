<?php

require(APPPATH.'libraries/REST_Controller.php');

class SwipIn extends REST_Controller{

    //Function to Register User
    public function register_post(){
        //Getting Data from Client
        $params = $this->post();

        //Check if Data Exists
        $query = $this->db->get_where('swip_user', array('user_name' => $params['name']));
        if($query->num_rows > 0){
            $this->response(array("code"=>"300","message"=>"User exists"), 300);
        }
        else{
            if($this->db->insert('swip_user',array("user_name"=>$params['name']))){
                $token = md5(time());
                $query_token = $this->db->insert("token",array("user"=>$params['name'],"token"=>$token));
                if($query_token){
                    $response = array("code"=>"201","message"=>"User Created","access_token"=>$token);
                }
                else{
                    $response = array("code"=>"202","message"=>"Some issue in getting you in");
                }
                $this->response($response, 201);
            }
            else{
                $this->response("Some Error in Mysql", 500);
            }
        }

    }

    //Function to Login User
    public function login_post(){

        $post_prams = $this->post();
        //Query to find user in DB
        $query = $this->db->get_where("swip_user", array("user_name"=>$post_prams['user_name']));

        if($query->num_rows == 1){
            $token = md5(time());
            $query_token = $this->db->insert("token",array("user"=>$post_prams['user_name'],"token"=>$token));
            if($query_token){
                $response = array("code"=>"200","message"=>"User found","access_token"=>$token);
            }
            else{
                $response = array("code"=>"202","message"=>"Some issue in login you in");
            }
        }
        else{
            $response = array("code"=>"300","message"=>"User not found");
        }

        $this->response($response);
    }

    public function apache_request_headers() {
        foreach($_SERVER as $key=>$value) {
            if (substr($key,0,5)=="HTTP_") {
                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                $out[$key]=$value;
            }else{
                $out[$key]=$value;
            }
        }
        return $out;
    }

    //Function to get all Users
    public function users_get(){
        //Receive Headers
//        if (!function_exists('apache_request_headers')) {
//            function apache_request_headers() {
//                foreach($_SERVER as $key=>$value) {
//                    if (substr($key,0,5)=="HTTP_") {
//                        $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
//                        $out[$key]=$value;
//                    }else{
//                        $out[$key]=$value;
//                    }
//                }
//                return $out;
//            }
//        }
        $headers = apache_request_headers();

//        print_r($headers);
//        echo phpinfo();
        if(!isset($headers['access_token'])){
            $response = array("code"=>"400","message"=>"Access token is required");
            $this->response($response);
        }
        else {
            //Find the token in DB
            $find_token = $this->db->get_where("token",array("token"=>$headers['access_token']));
            if($find_token->num_rows == 1){
                //Query to get all users
                $query = $this->db->get("swip_user");
                $result_array = array();
                if($query->num_rows > 0){
                    foreach($query->result() as $row){
                        array_push($result_array,$row);
                    }
                    $response = array("code"=>"200","message"=>$result_array);
                }
                else{
                    $response = array("code"=>"300","message"=>"No records found");
                }

                $this->response($response);
            }
            else{
                $response = json_encode(array("code"=>"401","message"=>"Invalid access token"));
//                echo ($response);
                $this->response(array("code"=>"401","message"=>"Invalid access token"), 200);
            }
        }
    }

    //Function to get Images from Google
    public function getImages_post(){
        $startOne = 1;$startTwo = 1;
        $responsArrayOne= array();
        $responsArrayTwo= array();
        $flag=false;

        //Getting Headers
        if (!function_exists('apache_request_headers')) {
            function apache_request_headers() {
                foreach($_SERVER as $key=>$value) {
                    if (substr($key,0,5)=="HTTP_") {
                        $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                        $out[$key]=$value;
                    }else{
                        $out[$key]=$value;
                    }
                }
                return $out;
            }
            $headers = apache_request_headers();
        }
        print_r($headers);

        if(!isset($headers['access_token'])){
            $response = array("code"=>"400","message"=>"Access token is required");
            $this->response($response);
        }
        else {
            //Find the token in DB
            $find_token = $this->db->get_where("token",array("token"=>$headers['access_token']));
            if($find_token->num_rows == 1){
                if ($this->post("cat_one") == NULL || $this->post("cat_two") == NULL) {
                    $this->response(array("code" => "300", "message" => "Category is required"));
                }
                else {
                    //Getting Category One Images
                    for ($startOne < 1; $startOne < 51; $startOne++) {
                        $url = 'https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=' . preg_replace('/\s+/', '%20', $this->post("cat_one")) . '&as_filetype=jpg&imgsz=small&rsz=8&start=' . $startOne;

                        // Initialize session and set URL.
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        $response = curl_exec($ch);
                        $newres = json_decode($response, true);
//        echo $response;

                        if (sizeof($newres['responseData']['results']) > 1) {
                            foreach ($newres['responseData']['results'] as $item) {
                                if (!in_array($item['url'], $responsArrayOne)) {
                                    array_push($responsArrayOne, array("name" => $this->post("cat_one"), "url" => $item['url']));
                                }
                            }
                        } else {
                            array_push($responsArrayOne, array("name" => $this->post("cat_one"), "url" => $newres['responseData']['results']['url']));
                        }

                        if ($startOne == 50) {
                            $flag = true;
                        } else {
                            $flag = false;
                        }
                        curl_close($ch);
                    }

                    //Getting Category Two Images
                    for ($startTwo < 1; $startTwo < 51; $startTwo++) {
                        $url = 'https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=' . preg_replace('/\s+/', '%20', $this->post("cat_two")) . '&as_filetype=jpg&imgsz=small&&rsz=8&start=' . $startTwo;

                        // Initialize session and set URL.
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        $response = curl_exec($ch);
                        $newres = json_decode($response, true);
//        echo $response;

                        if (sizeof($newres['responseData']['results']) > 1) {
                            foreach ($newres['responseData']['results'] as $item) {
                                if (!in_array($item['url'], $responsArrayTwo)) {
                                    array_push($responsArrayTwo, array("name" => $this->post("cat_two"), "url" => $item['url']));
                                }
                            }
                        } else {
                            $temp = array("name" => $this->post("cat_two"), "url" => $newres['responseData']['results']['url']);
                            array_push($responsArrayTwo, $temp);
                        }

                        if ($startTwo == 50) {
                            $flag = true;
                        } else {
                            $flag = false;
                        }
                        curl_close($ch);
                    }
//        Displaying Images
                    if ($flag == true) {
//            echo "One";
//            foreach($responsArrayOne as $item){
//                echo "<img src = '".$item."' width = '80px'/> <hr/>";
//            }
//            echo "Two";
                        $newRes = array_merge($responsArrayOne, $responsArrayTwo);
                        shuffle($newRes);
                        $this->response(array("code" => "200", "message" => $newRes));
//            foreach($newRes as $item){
//                echo "<img src = '".$item."' width = '80px'/> <hr/>";
//            }
                    }
                }
            }
            else{
                $response = json_encode(array("code"=>"401","message"=>"Invalid access token"));
//                echo ($response);
                $this->response(array("code"=>"401","message"=>"Invalid access token"), 200);
            }
        }


    }

}