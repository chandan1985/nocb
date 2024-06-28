<?php
class Zenfolio {
	private static $url = 'api.zenfolio.com/api/1.8/zfapi.asmx';
	private static $userAgent = 'SlideDeck3';
	var $token = null;
	var $loginName = null;
	var $cacheSeconds = 3600;

	function __construct () {
		/* removed caching for now */
	}

	private function call($method,$params,$try = false,$secure = false) {
		if(is_array($params)) {
			$params = json_encode($params);
		}
		$bodyString = "{\"method\": \"".$method."\",\"params\": ".$params.",\"id\": 1}";
		$bodyLength = strlen($bodyString);
		$headers = array();		//$headers[] = 'Host: api.zenfolio.com';
		//$headers[] = 'X-Zenfolio-User-Agent: '.self::$userAgent;
		if($this->token ) {
			$headers[] = 'X-Zenfolio-Token: '.$this->token;
		}
		$headers[] = 'User-Agent: '.self::$userAgent;
		$headers[] = 'Content-Type: application/json';
		$headers[] = 'Content-Length: '.$bodyLength;

		$protocol = $secure ? 'https' : 'http';

		$curl_connection = curl_init($protocol.'://'.self::$url);
		curl_setopt($curl_connection, CURLOPT_USERAGENT, self::$userAgent);
		curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl_connection, CURLOPT_HEADER, true);
		curl_setopt($curl_connection, CURLOPT_POST, true);
		curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $bodyString);
		curl_setopt($curl_connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_connection, CURLOPT_CRLF,true);
		curl_setopt($curl_connection, CURLOPT_VERBOSE, true);
		
		$result = curl_exec($curl_connection);
		curl_close($curl_connection);
		if ($result) { 
			$json = $json = json_decode(substr($result, strpos($result, '{') - 1),false,512,JSON_BIGINT_AS_STRING);
			$result = $json->result;
			if(!$result) {
				if($try) {
					return false;
				} else {
					return null;
				}
			}
			return $result;
		} else {
			if($try) {
				return false;
			} else {
				return false;
			}
		}
	}

	public function authenticatePlain($loginName, $password, $slidedeck) {
		$params = array($loginName,$password);
		$zenfolio_authenticate_token = get_transient("slidedeck_".$slidedeck['id']."_zenfolio_token_transient");
		try {
			if(empty($this->token))
			{
				$this->token = $zenfolio_authenticate_token;
			}				
			$this->token = $this->call('AuthenticatePlain',$params,true,true);
			$this->loginName = $loginName;
		} catch (ZenfolioException $e) {
			return false;
		}
		return $this->token;
	}

	public function getPopularPhotos($offset, $max) {
		$params = array($offset,$max);
		return $this->call('GetPopularPhotos',$params);
	}

	public function getRecentPhotos($offset, $max) {
		$params = array($offset,$max);
		return $this->call('GetRecentPhotos',$params);
	}

	public function loadGroup($groupId,$level='LEVEL1',$includeChildren=false) {
		$params = array($groupId,$level,$includeChildren);
		$group = $this->call('LoadGroup',$params,true);
		return $group;
	}

	public function loadGroupHierarchy($loginName) {
		$params = array($loginName);
		$groupHierarchy = $this->call('LoadGroupHierarchy',$params);
		return $groupHierarchy;
	}

	public function loadPhoto($photoId,$level='LEVEL1') {
		$params = array($photoId,$level);
		$photo = $this->call('LoadPhoto',$params);
		return $photo;
	}

	public function loadPhotoSet($photoSetId,$level='LEVEL1',$includePhotos=false) {

		$includePhotos = $includePhotos ? 'true' : 'false';
		$params = '['.$photoSetId.',"'.$level.'",'.$includePhotos.']';
		$photoSet = $this->call('LoadPhotoSet',$params);
		if($includePhotos > 1) {
			foreach ($photoSet->Photos as &$photo) {
				
				$photo = $this->loadPhoto($photo->Id,2);
			}
		}
		return $photoSet;
	}

	public function loadPublicProfile($loginName) {
		$params = array($loginName);
		$publicProfile = $this->call('LoadPublicProfile',$params);
		return $publicProfile;
	}

}
