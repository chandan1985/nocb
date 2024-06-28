<?php
/*
Legendary_Router
 Handle content based on paths / browser data
*/
class Legendary_Router
{
    private $basePg = null;
    private $output; // = "";
    private $listTitle = null;

    public function __construct()
    {
        $this->basePg = strtok(strtok(ltrim($_SERVER["REQUEST_URI"], "/"), "/"), "?");
    }

    private static function buildSearchStr($qs)
    {
        if (!(count($qs) >= 2 && $qs[1] == "search")) {
            return "";
        }
        $str = "";
        for ($i = 2; $i < count($qs); $i++) {
            $str .= ($str == "" ? "" : " ") . $qs[$i];
        }
        return $str;
    }

    public function getPageTitle($pgTitle)
    {
        if ($this->listTitle != null) {
            $pgTitle .= ' | ' . $this->listTitle;
        }
        return $pgTitle;
    }

    public function getListTitle($pgTitle)
    {
        if ($this->listTitle == null) {
            return $pgTitle;
        }
        return $this->listTitle;
    }

    // route() Based on passed details, get the appropriate details into $this->output for display().
    //    public function route($section)
    public function route($oAPI,$section)
    {
	$this->output = '';
        $qs = explode('/', trim($_SERVER['QUERY_STRING'], '/'));
        $options = Toolbox::get_option('legendary_options', []);
        if ($section == "main"):
            $this->output = '<div ldl-basepg="' . $this->basePg . '" style="display:none;"></div>';
            if (count($qs) >= 2 && $qs[0] == "d") {
                $iListId = $qs[1];
                $aDetails = $oAPI->getListDetails($iListId);
		$this->listTitle = @$aDetails["NAME"];
                $sViews = isset($aDetails["CHARTS"]) ? $aDetails["CHARTS"] : "";
                //$this->output .= '<h1 class="entry-title">'.$this->listTitle.'</h1>';
		if ( @$aDetails["ALTDESCR"] != "" ) {
			$this->output .= '<p>' . $aDetails["ALTDESCR"] . "</p>";
		} else if (@$aDetails["DESCR"] != "") {
			$this->output .= '<p>' . $aDetails["DESCR"] . "</p>";
                }
                $this->output .= '<div id="ldldata-main-content" ldl-dsearch="'.(@$aDetails["ALLOWSEARCH"]===TRUE?"true":"false").'" ldl-list-id="' . $iListId . '" ldl-views="' . $sViews . '" ldl-host="' . $options["host"] . '"></div>';
            } else {
                if (array_key_exists("qkey", $options) && $options["qkey"] != null && $options["qkey"] != "") {
                    $qKey = $options["qkey"];
                } else {
		    $qKey = "";
		}
                $this->output .= '<div id="ldldata-main-content" ldl-qkey="' . $qKey . '" ldl-estore ldl-host="' . $options["host"] . '"></div>';
            }
            $this->output .= '<script>if ( typeof LD == "object" ) { LD.initPage(); }</script>';
        endif;
    }

    public function display()
    {
        return $this->output;
    }
}
