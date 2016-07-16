<?php
if (!class_exists('tslib_pibase')) require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('ffpi_nodecounter').'class.RestAPI.php');

class tx_ffpinodecounter_pi1 extends tslib_pibase {
	public $prefixId      = 'tx_ffpinodecounter_pi1';        // Same as class name
	public $scriptRelPath = 'pi1/class.tx_ffpinodecounter_pi1.php';  // Path to this script relative to the extension dir.
	public $extKey        = 'ffpi_nodecounter';  // The extension key.

	function getNodes($path, $external){
		if($external === TRUE)
		{
			//API Aufrufen
			$RestAPI = new RestAPI();
			//Pfad zur nodelist
			$RestAPI->setRequestApiUrl($path);
			$RestAPI->setRequestMethod('get');
			$RequestHeader = array('Accept: application/json');
			$RestAPI->setRequestHeader($RequestHeader);
			$request = $RestAPI->sendRequest();
			$statuscode = $RestAPI->getStatusCode();
			if($request AND $statuscode == '200')
			{
				$nodes = $RestAPI->getArray();
				$nodes = $nodes["nodes"];
				return $nodes;
			}
			else
			{
				return false;
			}
		}
		else
		{
			$json = file_get_contents($path);
			$nodes = json_decode($json, true);
			return $nodes;
		}
	}
	
	/**
	 * Counting Nodes
	 * @param array $nodes The nodes JSON as an Array
	 * @return array number of nodes
	 */
	function countNodes($nodes){
		$count = array();
		$count['online'] = 0;
		$count['offline'] = 0;
		$count['total'] = 0;
		$count['clients'] = 0;

		for($i = 0; $i < count($nodes); ++$i) {
			if($nodes[$i]['status']['online'] === TRUE AND $nodes[$i]['role'] != 'gate')
			{
				$count['online']++;
				$count['total']++;
			}
			elseif($nodes[$i]['role'] != 'gate')
			{
				$count['offline']++;
				$count['total']++;
			}

			if(is_numeric($nodes[$i]['status']['clients']))
			{
				$count['clients'] = $count['clients'] + $nodes[$i]['status']['clients'];
			}
		}

		return $count;
	}

	/**
	 * The main method of the Plugin.
	 *
	 * @param string $content The Plugin content
	 * @param array $conf The Plugin configuration
	 * @return string The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		$this->pi_USER_INT_obj=1;   // Cache für dieses Element abschalten

		// Config
		$this->templateHtml = $this->cObj->fileResource($conf['templateFile']);
		$this->nodeListFile = $conf['nodeListFile'];
		$this->nodeListExternal = $conf['nodeListExternal'];
		// Template
		$subpart = $this->cObj->getSubpart($this->templateHtml, '###COUNTER###');
		$markerArray = array();

		// Get Data
		if($this->nodeListExternal == 'TRUE')
		{
			$external = TRUE;
		}
		else
		{
			$external = FALSE;
		}
		$nodes = $this->getNodes($this->nodeListFile,$external);
        if($nodes != FALSE)
        {
            $count = $this->countNodes($nodes);
            // Write data in Template vars
            $markerArray['###COUNTER-TOTAL###'] = $count['total'];
            $markerArray['###COUNTER-ONLINE###'] = $count['online'];
            $markerArray['###COUNTER-OFFLINE###'] = $count['offline'];
            $markerArray['###COUNTER-CLIENTS###'] = $count['clients'];
            // Write labels in Template vars
            $markerArray['###LL-NODES-ONLINE###'] = $this->pi_getLL('nodes-online');
            $markerArray['###LL-NODES-OFFLINE###'] = $this->pi_getLL('nodes-offline');
            $markerArray['###LL-CLIENTS###'] = $this->pi_getLL('clients');
            $markerArray['###LL-OF###'] = $this->pi_getLL('of');

            // Create the content by replacing the content markers in the template
            $content = $this->cObj->substituteMarkerArray($subpart, $markerArray);
        }
        else
        {
            //no data
            $content = '';
        }





		return $this->pi_wrapInBaseClass($content);
	}
}
if(defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ffpi_nodecounter/pi1/class.tx_ffpinodecounter_pi1.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ffpi_nodecounter/pi1/class.tx_ffpinodecounter_pi1.php']);
}
?>
