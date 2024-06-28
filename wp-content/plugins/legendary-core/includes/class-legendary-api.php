<?php
/*
Legendary_API

Class emulating basic features of the Legendary Data (Meroveus) API object

*/

class Legendary_API
{
    /* core vars controlling remote data access */
    private $aKey;
    private $eKey;
    private $host;
	private $isPremium = false;

    /* CONSTRUCTOR */
    public function __construct($aKey, $eKey, $host)
    {
        $this->aKey = $aKey;
        $this->eKey = $eKey;
        $this->host = $host;

	/* 20220425 tdf move the following to inside constructor to prevent plugin collisions */

	$_GET = stripslashes_deep($_GET);
	$_POST = stripslashes_deep($_POST);
	$_COOKIE = stripslashes_deep($_COOKIE);
	$_REQUEST = stripslashes_deep($_REQUEST);
    }

	public function setPremium( $b ) {
		$this->isPremium = $b;
	}

	public function hasPremiumAccess() {
		return $this->isPremium;
	}

	private static function postEncode( $str ) {
		$str = str_replace( '+', '%2B', $str );
		$str = str_replace( '=', '%3D', $str );//protect against equals sign
		$str = str_replace( '&', '%26', $str );//protect against equals ampersands
		$str = str_replace( '%', '%25', $str );//protect against equals %
		return $str;
	}

    public static function request($data, $host, $sParam = "MCORE")
    { 
	$json = self::postEncode( json_encode($data) );
        $ch = curl_init(); // init curl
        $sMeroveus = $host . "/api/";
        
	// set the target url
        curl_setopt($ch, CURLOPT_URL, $sMeroveus);

        // how many parameters to post
        curl_setopt($ch, CURLOPT_POST, 1);
        // this just for a bad response
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sParam . "=" . $json);
        curl_setopt($ch, CURLOPT_HEADER, 0); // DO NOT RETURN HTTP HEADERS
	
       	/* 20200615 tdf added the following two lines to prevent website hangs */ 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // RETURN THE CONTENTS OF THE CALL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //ignore protocol if https
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //ignore ssl name

        $result = curl_exec($ch);
        if ($result === false) {
            //$txt = 'Curl error: ' . curl_error($ch);
            return array("ERROR" => "Unreachable Desitination");
        }

        curl_close($ch);
        $res = self::is_gzcompressed($result) ? gzdecode($result) : $result;
        $resp = json_decode($res, true);
        return $resp;
    }

    /* static method to test whether data returned is compressed: added 20210108 to accommodate cloudflare auto-decompression */
    private static function is_gzcompressed( $str ) {
	return (substr($str, 0, 3) == "\x1f\x8b\x08");
	//return !(strpos($str, '{"') <= 1);
    }

    /* static method for de-gzencoding data returned from meroveus */
    private static function gzdecode($data, &$filename = '', &$error = '', $maxlength = null)
    {
        return @gzinflate(substr($data, 10, -8));
    }

    /* relay posted payload through server to desitination meroveus endpoint */
    public function relay()
    {
        $param = "MCORE";
        $method = "POST";
	if ( isset( $_POST["legendaryParam"] ) ) {
		/* allow custom override of param */
		$param = $_POST["legendaryParam"];
        } else if (isset($_POST["MYTHYR"])) {
		$param = "MYTHYR";
        } else if (isset($_POST["MCORE"])) {
		$param = "MCORE";
        } else {
		$method = "GET";
        }
        $response = '{"ERROR":"Relay invalid"}';
        if ( empty($_POST) && isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "estore" ) {
		$path = rtrim($this->host,'/') . $_REQUEST["url"];
		//$response = file_get_contents( $this->host . "/" . $_REQUEST["url"] );
		$path .= "?akey=".$this->aKey;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $path);
		$response = curl_exec($ch);
		curl_close($ch);
	} else if ($method == "POST") { // \/ Handle for empty $data
            $datac = $_POST[$param];
            $data = json_decode($datac);
		if ( $data == null ) {
			return '{"ERROR":"Relay error", "TEXT":"'.$datac.'"}';
		}
		$data = stripslashes_deep( $data );
            $data->AKEY = $this->aKey;
            $data->EKEY = $this->eKey;
		$aResponse = self::request($data, $this->host, $param);

            if (isset($aResponse["FILENAME"])) {
                $aResponse["HOST"] = $this->host;
            }
            $response = json_encode($aResponse);
        } else {
            $response = '{"ERROR":"GET Requests not supported in relay."}';
        }
	header('Content-Type: application/json');
        return $response;
    }

	/* 20200428 tdf added the following method to simplify server-side calls from ldl api obj */
	public function signedRequest( $aData, $sParam="MCORE" ) {
		$aData["AKEY"] = $this->aKey;
		$aData["EKEY"] = $this->eKey;
		return self::request( $aData, $this->host, $sParam );
	}

    public function getLabelsByClass($dclass)
    {
        $req = array("MODE" => "LABELSEARCH", "AKEY" => $this->aKey, "EKEY" => $this->eKey, "LABELKEY" => $dclass, "LABELVAL" => "*");
        $ar = self::request($req, $this->host);
        return $ar["LABELS"];
    }

    public function getResolveWidget($basePg, $postId, $sTitle, $sDate)
    {
        $html = '<div id="ldlresolve-main-content" ldl-basepg="' . $basePg . '" ldl-host="' . $this->host . '" ldl-postid="' . $postId . '" ldl-title="' . $sTitle . '" ldl-date="' . $sDate . '" ldl-resolve></div>';
        $html .= '<script>if ( typeof LD == "object" ) { LD.initPage(); }</script>';
        //<script>$MYTHYR.resolve({"prefix":"ldlresolve", "host":"'. $this->host .'", "pageId":"'. $postId .'", "pageTitle":"'.$sTitle.'", "pageDt":"'.$sDate.'"});</script>';
        return $html;
    }

    public function getListDetails($iListId)
    {
        $req = array("MODE" => "ESTORESEARCH", "REMOTEHOST"=>$_SERVER["HTTP_HOST"], "AKEY" => $this->aKey, "EKEY" => $this->eKey, "LIST" => array("LISTID" => $iListId), "MAXROWS" => 1);
        $aResp = self::request($req, $this->host, "MYTHYR");
        $ar = @$aResp[0];
	$aDetails = "";
        if (isset($ar["LIST"]) && is_array($ar["LIST"])) {
		$aList = $ar["LIST"];
		$sTimeframe = $aList["TIMEFRAME"];
		$aDetails = array("LISTID" => $aList["LISTID"], "NAME" => $ar["NAME"]);
		if (isset($aList["SUBTITLE"])) {
			$aDetails["SUBTITLE"] = $aList["SUBTITLE"];
		}
		if (isset($aList["PROPERTIES"])) {
			foreach ($aList["PROPERTIES"] as $aProp) {
				$aDetails[$aProp["KEY"]] = $aProp["VAL"];
				if ($aProp["KEY"] == "PUBTIMEFRAME") {
					$sTimeframe = $aProp["VAL"];
				}
			}
		}
		$aDetails["TIMEFRAME"] = $sTimeframe;

		$sDescr = array_key_exists("DESCR", $ar) ? $ar["DESCR"] : "";
                $sAltDescr = array_key_exists("ALTDESCR", $ar) ? $ar["ALTDESCR"] : "";

		$aDetails["DESCR"] = $sDescr;
		$aDetails["ALTDESCR"] = $sAltDescr;
		if (isset($aList["META"])) {
			$aMeta = $aList["META"];
			$aDetails["RECTYP"] = $aMeta["RECTYP"];
		}
		if ( !$this->isPremium ) {
			$aDetails["CHARTS"] = "list";
			$aDetails["ALLOWSEARCH"] = false;
		} else {
			$aDetails["ALLOWSEARCH"] = true;
		}
        }

        return $aDetails;
    }

    public function getTopRecs($iListId, $iMax = 10, $bFullResponse=false)
    {
        $req = array("MODE" => "SEARCH", "AKEY" => $this->aKey, "EKEY" => $this->eKey, "LIST" => array("LISTID" => $iListId), "MAXROWS" => $iMax);
        $ar = self::request($req, $this->host);
	if ( $bFullResponse ) {
		return $ar;
	}
	$aRecs = isset( $ar["SET"]["RECS"] ) ? $ar["SET"]["RECS"] : array();
        return $aRecs;
    }

	public function getRec( $iRecId ) {
		$req = array("MODE" => "SEARCH", "AKEY" => $this->aKey, "EKEY" => $this->eKey, "KEYWORDS"=>"ID:${iRecId}", "MAXROWS" => 1);
		$ar = self::request($req, $this->host);
		return $ar["SET"]["RECS"][0];
	}

    public function getDetails($iRecId, $iListId)
    {
        $req = array("REC" => array("ID" => $iRecId),
            "LIST" => array("LISTID" => $iListId),
            "MODE" => "RECVIEW",
            "AKEY" => $this->aKey,
            "EKEY" => $this->eKey,
        );
        $aResponse = self::request($req, $this->host, "MYTHYR");
        return $aResponse["HTML"];
    }
}
