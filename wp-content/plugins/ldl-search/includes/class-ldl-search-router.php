<?php

class LDL_search_router {

    private $output = "";

    public function __construct() {

    }

    public function route($lAPI, $section) {

        $core_options = Toolbox::get_option('legendary_options', []);
	$search_options = Toolbox::get_option('ld_search_options', []);       
 
        if ($section == "ldl_search_stats") {

            $data = array("MODE" => "ESEARCHCOUNTS");
            $msg = $lAPI->signedRequest($data, "MYTHYR");

            $this->output = '<div class="ldl-search-stats-div">';
            foreach ($msg['COUNTS'] as $i => $c) {
                $this->output .= '<div class="ldl-search-stat-circle"><div class="ldl-stat-content"><span class="ldl-stat-count">' . $c['COUNT'] . '</span><span class="ldl-stat-name">' . $c['NAME'] . '</span></div></div>';
            }
            $this->output .= '</div>';
            

        }

        if ($section === "ldl_search_form") {

            $search_fields = json_decode(Toolbox::get_option('ld_search_options', [])['ld-search-ordered-fields']);
            $form = $this->ld_search_form($search_fields, $lAPI->hasPremiumAccess(), $search_options, $core_options );
           
            // enqueue these 
            wp_enqueue_script('ld-search-js');
            wp_enqueue_script('ld-search-s2');
            wp_enqueue_script('ld-search-s2-jq-ui');
            wp_enqueue_script('ld-s2-adapter');
            wp_enqueue_style('ld-search-s2');
            wp_enqueue_style('ld-s2-adapter');

            $this->output = '
            <div>' . $form . '
            

            </div>
            
            ';
        }
	if ( $section == "ldl_search_results" && !$lAPI->hasPremiumAccess() ) {
		if ( strlen( $search_options['ld-search-subscribe-page'] ) > 0 ) {
			$this->output = '<script>window.location.href=\''.$search_options['ld-search-subscribe-page'].'\';</script>';
		} else {
			$this->output = '<div>Your subscription level does not support access to this content.</div>';
		}
	} else if ($section === "ldl_search_results") {
            $host = $core_options['host'];
            
            $companyListIdFromSettings = $search_options['ld-search-business-list'];
            $personListIdFromSettings = $search_options['ld-search-executive-list'];

		$selectToggle = isset( $_POST[ "ldl-custom-search-toggle" ] ) ? $_POST[ "ldl-custom-search-toggle" ] : "company";

            $this->output = '
            <div class="ldl-custom-search-results" custom-search-view="'.$selectToggle.'">
                <!--<div class="toggle-select-wrapper">
                    <label>Display results by: </label>
                    <select id="ldl-custom-search-toggle">
                        <option value="company" selected>Companies</option>
                        <option value="executive">Executives</option>
                    </select>
                </div>-->

                <div class="ldl-custom-search" id="ldatacustomcompanysearch-main-content" ldl-lib-host="' . $host . '" ldl-list-id="' . $companyListIdFromSettings . '" mythyr-widget-guid="ldatacustomcompanysearch" ldl-chart-options="">
                </div>

		<div class="ldl-custom-search" id="ldatacustomexecutivesearch-main-content" ldl-lib-host="' . $host . '" ldl-list-id="' . $personListIdFromSettings . '" mythyr-widget-guid="ldatacustomexecutivesearch" ldl-chart-options="">
                </div>
                
            </div>

            ';
        }
    }

    private function ld_search_form($fields, $bPremiumAccess=false, $search_options, $core_options ) { 
        $action = $bPremiumAccess ? $search_options['ld-search-results-page'] : $search_options['ld-search-subscribe-page'];
	if ( substr( strtolower($action), 0, 1 ) != "http" && substr( $action, 0, 1 ) != "/" ) $action = "/" . $action;

	/* pass through to js the fieldkey pair -- will need when building the filter */
	$sKeyPair = $search_options[ 'ld-container-field-pair' ];

	$rv = '<div class="ld-search-form" ><form id="ldl_custom_search_form" ldl-keypair="'.$sKeyPair.'" action="'.$action.'" method="post">';

        foreach ($fields as $i => $field) {
            $rv .= '<div class="ld-fld-div">';
            $rv .= '    <label class="ld-search-field-label">' . $field->fld_name . '</label>';
            $rv .= '    <div class="ld-inp-div">';
            $rv .= $this->ld_search_input($field);
            $rv .= '    </div>';

            if ($field->fld_type == "Label") {
                $rv .= '    <div class="ld-lbl-err" data-fldname="' . $field->fld_id . '"></div>';
                $rv .= '    <div class="ld-lbl-selections_' . $field->fld_id . '"></div>';
            }
            $rv .= "</div>";
        }

	$iBizListId = $search_options["ld-search-business-list"];
	$iExecListId = $search_options["ld-search-executive-list"];

	$selectToggle = isset( $_POST[ "ldl-custom-search-toggle" ] ) ? $_POST[ "ldl-custom-search-toggle" ] : "company";

	$rv .= '<div class="ld-fld-div">
<label class="ld-search-field-label">Filter results by: </label>
<div class="ld-inp-div">
<select id="ldl-custom-search-toggle" name="ldl-custom-search-toggle">
<option value="company" ld-list-id="'.$iBizListId.'"'.($selectToggle=="company"?" selected":"").'>Businesses</option>
<option value="executive" ld-list-id="'.$iExecListId.'"'.($selectToggle=="executive"?" selected":"").'>Executives</option>
</select></div>
</div>';

	$rv .= '<input type="hidden" value="' . (isset($_POST["ldl-posted-search-filter"]) ? htmlentities($_POST["ldl-posted-search-filter"]) : "") . '" name="ldl-posted-search-filter" id="ldl-posted-search-filter" />';

	$rv .= '</form></div>'; // ends the ld-search-form div

	//$rv .= '<div id="mythyr-host-container" style="display:none;">'.$core_options["host"].'</div>';

        $rv .= '<button id="ld-search-btn" class="ld-search-btn">Search</button>';
	$rv .= '<button id="ld-download-btn" style="display:none;" class="ld-search-btn" ld-premium-access="'.($bPremiumAccess?"true":"false").'">Download</button>';
        
        return $rv;
    }

    

    private function ld_search_input($fld) {
        if ($fld->fld_type == "Numeric") {
            $rv =  '    <div class="ld-numeric-range" ldl-rectyps="'.$fld->fld_rectyps.'" data-fldkey="' . $fld->fld_id . '">';
		$low = isset( $_POST[ $fld->fld_id."-low" ] ) ? $_POST[ $fld->fld_id."-low" ] : "";
		$high = isset( $_POST[ $fld->fld_id."-high" ] ) ? $_POST[ $fld->fld_id."-high" ] : "";
            $rv .= '        <input class="ld-inp-numeric ld-numeric-low" name="'.$fld->fld_id.'-low" type="text" value="'.$low.'">';
            $rv .= '        <span>to</span>';
            $rv .= '        <input class="ld-inp-numeric ld-numeric-high" name="'.$fld->fld_id.'-high" type="text" value="'.$high.'">';
            $rv .= '    </div>';
        } 

        if ($fld->fld_type == "Text") {
		$val = isset( $_POST[ $fld->fld_id ] ) ? $_POST[ $fld->fld_id ] : "";
            $rv = '    <input ldl-rectyps="'.$fld->fld_rectyps.'" class="ld-inp-text" type="text" name="' . $fld->fld_id . '" value="'.htmlentities($val).'">';
        }
        if ($fld->fld_type == "Label") {
            $rv = '     <select ldl-rectyps="'.$fld->fld_rectyps.'" class="ld-inp-select" name="' . $fld->fld_id . '[]"';
		if ( isset( $_POST[ $fld->fld_id ] ) ) {
			$rv .= ' ld-posted-val="'.implode("`",$_POST[ $fld->fld_id ]).'"';
		}
		$rv .= '>';
            // $rv .= '        <option></option>';
            $rv .= '    </select>';
        }

        return $rv;
    }

    
    public function display() {
        return $this->output;
    }
}
