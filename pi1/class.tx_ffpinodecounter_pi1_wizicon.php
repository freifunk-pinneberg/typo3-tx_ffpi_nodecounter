<?php
class tx_ffpinodecounter_pi1_wizicon {

	/**
	 * Processing the wizard items array
	 *
	 * @param array $wizardItems The wizard items
	 * @return array Modified array with wizard items
	 */
	public function proc(array $wizardItems) {
		$LL = $this->includeLocalLang();

		$wizardItems['plugins_tx_ffpinodecounter_pi1'] = array(
			'icon' => t3lib_extMgm::extRelPath('ffpi_nodecounter') . 'pi1/ce_wiz.png',
			'title' => $GLOBALS['LANG']->getLLL('pi1_title', $LL),
			'description' => $GLOBALS['LANG']->getLLL('pi1_plus_wiz_description', $LL),
			'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=ffpi_nodecounter_pi1'
		);

		return $wizardItems;
	}

	/**
	 * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array found in that file.
	 *
	 * @return array The array with language labels
	 */
	protected function includeLocalLang() {
		$llFile = t3lib_extMgm::extPath('ffpi_nodecounter') . 'locallang.xml';
		$version = class_exists('t3lib_utility_VersionNumber')
				? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
				: t3lib_div::int_from_ver(TYPO3_version);
		if ($version < 4006000) {
			$LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS['LANG']->lang);
		} else {
			/** @var $llxmlParser t3lib_l10n_parser_Llxml */
			$llxmlParser = t3lib_div::makeInstance('t3lib_l10n_parser_Llxml');
			$LOCAL_LANG = $llxmlParser->getParsedData($llFile, $GLOBALS['LANG']->lang);
		}

		return $LOCAL_LANG;
	}
}



if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ffpi_nodecounter/pi1/class.tx_ffpinodecounter_pi1_wizicon.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ffpi_nodecounter/pi1/class.tx_ffpinodecounter_pi1_wizicon.php']);
}

?>