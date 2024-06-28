<?php
//error_reporting(E_ALL);
//ini_set('display_error',1);
class actonClass {
	static $options = null;
	
    public function __construct() {
        
    }
    
	
    //Below is the function which returns access token, refresh token expired time
    public function get_access_token($base_url, $_auth) {
        
        $ch = curl_init();
        $header = array(
            'POST',
            'HTTP/1.1',
            'Host: restapi.actonsoftware.com',
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $base_url . '/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Marketo Proxy 1.1.0)");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_auth));
		$results =  json_decode(curl_exec($ch));
		
		
		//$results = '{"token_type":"bearer","expires_in":3600,"refresh_token":"d2accd75fae08a6941376d23591336","access_token":"dec9670bc1e477da1cb9a73dfaf5110"}';
		//$results = json_decode($results);
		
        $refresh_token = $results->refresh_token;
        $expires_at = $results->expires_in;
        $access_token = $results->access_token;
        
        if (isset($refresh_token) && isset($access_token)){
            
            
            if (isset($expires_at) && $expires_at){
                    $expires_at = mktime(date("H"), date("i"), date("s")+$expires_at, date("m"), date("d"), date("Y"));
            }else
				$expires_at = mktime(date("H"), date("i"), date("s")+3600, date("m"), date("d"), date("Y"));
        } 

        /* stdClass Object ( [token_type] => bearer [expires_in] => 3600 [refresh_token] => 2e45bcc9c8a95378e9cdede5184536f [access_token] => 4baf25bfee90169dc66b978ebd31041 ) */
        $result = array('refresh_token' => "$refresh_token", 'access_token' => "$access_token", 'expires_at' => $expires_at);
        //$result = array('refresh_token' => "17a5fd88241642bcf9ac37ab8d381c", 'access_token' => "ca137dca70eecc5be3bc6d7c573af788", 'expires_at' => "3600");
        //$errors = curl_error($ch);
        //curl_close($ch);
		/*print_r( $result);
        print_r( $results);
		exit;*/
        return $result;
    }

    //function update_contact_info($base_url, $access_token, $list_id, $email, $data_string) {
   public function update_contact_info($list_id,$email,$data_string) {
	   
	    $setting = $this->acton_details();
		$access_token = $this->check_token();
        $base_url =$setting['btm_acton_end_point'];
		
        $ch = curl_init("$base_url/api/1/list/$list_id/record?email=$email");

        $header = array(
            "PUT /api/1/list/$list_id/record?email=$email",
            'HTTP/1.1',
            'Host: restapi.actonsoftware.com',
            "Authorization: Bearer $access_token",
            'Cache-Control: no-cache',
            'Content-Type: application/json',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        $result = curl_exec($ch);
         
        //print_r($list_id, $email);exit;
        $errors = curl_error($ch);
		print_r( $errors);
        return json_decode($result);
    }

    function get_headers($base_url, $access_token, $list_id) {
        $ch = curl_init();
        $headers = array(
            'GET',
            "Authorization: Bearer $access_token",
            "Cache-Control: no-cache"
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $base_url . "/api/1/list/$list_id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Marketo Proxy 1.1.0)");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //curl_setopt($che, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_auth));
        $lists = json_decode(curl_exec($ch));
        $header = $lists->headers;
        //$errors = curl_error($ch);
        curl_close($ch);
        return $header;
    }
	
	function userlookup($user_email,$list_id){
		$ch = curl_init("https://restapi.actonsoftware.com/api/1/list/lookup/$list_id?email=$user_email");  
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                   
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $this->check_token(),'Content-Type: application/json',)); 
		$result = curl_exec($ch);
		return $result_s = json_decode($result,true);
	}
	
    function get_fields_info($base_url, $access_token, $list_id,$user_email,$contactID='') {
        
		
		$user = $this->userlookup($user_email,$list_id);
		return $user;
		
		if(isset($user['contactID']) && $user['contactID'] !=='')
			$contactID =	$user['contactID'];
		else 
			return false;
				
        $ch = curl_init();
        //$acton_obj = new acton_class();
        $headers = array(
            'GET',
            "Authorization: Bearer $access_token",
            "Cache-Control: no-cache"
        );
		//echo $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $base_url . "/api/1/list/$list_id/record/$contactID");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Marketo Proxy 1.1.0)");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        //curl_setopt($che, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_auth));
        $fields_info = json_decode(curl_exec($ch));
        $headers_info = $this->get_headers($base_url, $access_token, $list_id);
        print_r($headers_info);exit;
		$errors = curl_error($ch);
		//print_r($errors);
		//print_r($fields_info);exit;
        $merged_content = array_combine($headers_info, $fields_info);
        
        curl_close($ch);
        return $merged_content;
    }

function acton_details() {
    $results = get_option("btm_acton_details");    
    return $results;
}

	private static function _load_option() {
		self::$options = wp_parse_args( json_decode(get_option( 'acton_auth_response' ),true), array('refresh_token' => "", 'access_token' => "", 'expires_at' => "") );
		
		return (object) self::$options;
	}
	

function check_token() {
    
   
    
    //Creting an Object
    
    //Getting acton auth response from database
    $get_auth_data = $this->_load_option();//get_option('acton_auth_response');
	
	//print_r($get_auth_data);exit;
    $get_acton_auth_data = $get_auth_data;//json_decode();
    $refresh_token = trim($get_acton_auth_data->refresh_token);
    $access_token = trim($get_acton_auth_data->access_token);
    $expires_at = trim($get_acton_auth_data->expires_at);

        //checking access token is expired or not
    if (empty($expires_at) || empty($access_token) || time() > $expires_at) { 
    
		 //calling a function to get details from database
		$acton_details = $this->acton_details();
		$acton_data_store = '';
		$base_url = $acton_details['btm_acton_end_point'];
		$site_id = $acton_details['btm_siteid'];
		$_auth = array(
			'client_id' => $acton_details['btm_acton_client_id'],
			'client_secret' => $acton_details['acton_secret_key'],
		);
		$_auth['username'] = $site_id.$acton_details['acton_user_name'];
		$_auth['password'] = $acton_details['acton_password'];
    
    
       if ($refresh_token) {
           $_auth['grant_type'] = 'refresh_token';
           $_auth['refresh_token'] = $refresh_token;           
           $access_token_val = $this->get_access_token($base_url, $_auth);               
           $access_token_data = json_encode($access_token_val);            
           update_option('acton_auth_response', $access_token_data);
       } else {
            $_auth['grant_type'] = 'password';     
            $access_token_val = $this->get_access_token($base_url, $_auth);
            $access_token_data = json_encode($access_token_val);
            //add_option('acton_auth_response', "$access_token_data", '', 'yes');
            update_option('acton_auth_response', $access_token_data);
        }
        
        $access_token_value = json_decode($access_token_data);
        
        $access_token_val = $access_token_value->access_token;
       
    } else {    
        $access_token_val = $access_token;
    }
    return $access_token_val;
}

// function create_new_contact($access_token,$base_url,$list_id) {
    // $che = curl_init();
    // $headers = array(
        // "POST",
        // "Authorization: Bearer $access_token",
        // "Content-Type: application/json",
        // "Cache-Control: no-cache"
    // );
    
    // $params = array('First Name'=>'John','Last Name'=>'Smith','E-mail Address'=>'testing@asentech.com');
    
    // print_r($headers);
    // $listId = "$list_id";
    // curl_setopt($che, CURLOPT_HTTPHEADER, $headers);
    // curl_setopt($che, CURLOPT_URL, $base_url . "/api/1/list/l-0002/record");
    // curl_setopt($che, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($che, CURLOPT_CONNECTTIMEOUT, 3000);
    // curl_setopt($che, CURLOPT_TIMEOUT, 3000);
    // curl_setopt($che, CURLOPT_POST, 1);
    // curl_setopt($che, CURLOPT_POSTFIELDS, json_encode($params));
// //    curl_setopt($che, CURLOPT_POSTFIELDS, http_build_query($_auth));
    // $results = curl_exec($che);
    // $errors = curl_error($che);
    // curl_close($che);
    // print_r($results);
    // print_r($errors);
    // exit;
    // //print_r($base_url .'/api/1/list/' . $listId . '/record?email=krishna@asentech.com');
    // //print_r($results);exit;
    // return $results;
// }

			function create_new_contact($access_token,$base_url,$list_id,$fname,$lname,$email,$SourceCodeField,$SourceCode,$sourceTypeField,$sourceType,$results) {
						$_data = array();
						$_data['First Name'] = $fname;
						$_data['Last Name'] = $lname;
						$_data[$SourceCodeField] = $SourceCode ? : '';
                        $_data[$sourceTypeField] = $sourceType ? : '';
						if($_REQUEST['exit_intent'] == true){
							foreach ($results['display_name_exit'] AS $key => $value) { 
								if(!empty($value)){
									$_data[$value] = 'TRUE';
								}
							}
						}else{ 
							foreach ($results['display_name'] AS $key => $value) { 
								if(!empty($value)){
									$_data[$value] = 'TRUE';
								}
							}
						}
									
						$ch = curl_init();
						$header = array(
							"PUT /api/1/list/$list_id/record?email=$email",
							'HTTP/1.1',
							'Host: restapi.actonsoftware.com',
							"Authorization: Bearer $access_token",
							'Cache-Control: no-cache',
							'Content-Type: application/json',
						);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
						curl_setopt($ch, CURLOPT_URL, $base_url . "/api/1/list/" . $list_id . "/record?email=".$email);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
						curl_setopt($ch, CURLOPT_TIMEOUT, 300);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
						curl_setopt($ch, CURLOPT_HEADER, false);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_data));
						$result = curl_exec($ch);
						$errors = curl_error($ch);
						curl_close($ch);
						return $result;
				}

		
function delete_contact($access_token,$base_url) {
    $ch = curl_init();
    $headers = array(
        'DELETE',
        'HTTP/1.1',
        "Authorization: Bearer $access_token",
        "Content-Type: application/json",
        "Cache-Control: no-cache"
    );
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $base_url . "/api/1/list/l-0002/record/l-0002:1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
    $results = curl_exec($ch);
    print_r($results);exit;
    return $results;
}
function objToArray($obj, &$arr){

    if(!is_object($obj) && !is_array($obj)){
        $arr = $obj;
        return $arr;
    }

    foreach ($obj as $key => $value)
    {
        if (!empty($value))
        {
            $arr[$key] = array();
            objToArray($value, $arr[$key]);
        }
        else
        {
            $arr[$key] = $value;
        }
    }
    return $arr;
}	
	
}

