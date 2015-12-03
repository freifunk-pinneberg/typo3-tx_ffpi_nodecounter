<?php
/*
 * Eine kleine Klasse zum ansteuern einer REST API
 */
class RestAPI {
	protected $requestApiUrl;
	protected $requestData;
	protected $requestHeader;
	protected $requestMethod;
	protected $requestConnectTimeout;

	protected $responseRawData;
	protected $responseJsonData;
	protected $responseArrayData;
	protected $responseStatusCode;
	protected $responseCurlStatus;

	public function __construct() {
		$this -> setRequestMethod();
		$this -> setRequestConnectTimeout();
	}

	/*
	 * Prüfen ob es sich ums JSON Format handelt.
	 */
	private function isJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	public function setRequestApiUrl($requestApiUrl = 'http://localhost') {
		return $this -> requestApiUrl = $requestApiUrl;
	}

	public function setRequestData($dataArray) {
		if (!is_array($dataArray))
		{
			trigger_error('$dataArray ist kein Array', E_USER_ERROR);
			return FALSE;
		}

		$i = 0;
		foreach ($dataArray as $key => $value)
		{
			$dataString = '';
			$tempDataString = $key . '=' . $value;
			if ($i = 0)
			{
				$dataString = $tempDataString;
			}
			else
			{
				$dataString .= '&' . $tempDataString;
			}
			$i++;
		}
		return $this -> requestData = $dataString;
	}

	public function setRequestHeader($requestHeader) {
		if (is_array($requestHeader))
		{
			return $this -> requestHeader = $requestHeader;
		}
		else
		{
			trigger_error('Request Header konnte nicht gesetzt werden', E_USER_WARNING);
			return FALSE;
		}

	}

	/**
	 * setRequestMethod
	 *
	 * @param string $requestMethod 'get' oder 'post'
	 * @return boolean
	 * @author
	 */
	public function setRequestMethod($requestMethod = 'get') {
		return $this -> requestMethod = strtolower($requestMethod);
	}

	public function setRequestConnectTimeout($timeout = '5') {
		if (is_numeric($timeout) AND $timeout >= 0)
		{
			$this -> requestConnectTimeout = $timeout;
		}
	}

	public function getRawData() {
		return $this -> responseRawData;
	}

	public function getJson() {
		$rawData = $this -> getRawData();
		if ($this -> isJson($rawData))
		{
			return $rawData;
		}
		else
		{
			//@TODO: Eventuell auf andere Formate prüfen und zu json umwandeln
			return false;
		}
	}

	public function getArray() {
		$json = $this -> getJson();
		if ($json !== FALSE)
		{
			$array = json_decode($json, TRUE);
			return $array;
		}
		else
		{
			return FALSE;
		}

	}

	public function getStatusCode() {
		return $this -> responseStatusCode;
	}

	public function getCurlStatus() {
		if (isset($this -> responseCurlStatus))
		{
			return $this -> responseCurlStatus;
		}
		else
		{
			return NULL;
		}
	}

	public function sendRequest() {
		if (!isset($this -> requestApiUrl) OR $this -> requestApiUrl == '')
		{
			trigger_error('keine API URL', E_USER_ERROR);
			return FALSE;
		}

		if (!isset($this -> requestMethod) OR $this -> requestMethod == '')
		{
			$this -> setRequestMethod();
		}

		$curl = curl_init($this -> requestApiUrl);
		//Im Erfolgsfall nicht TRUE sondern Daten zurückliefern
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

		//Useragent
		curl_setopt($curl, CURLOPT_USERAGENT, 'TYPO3 at ' . $_SERVER['HTTP_HOST']);

		//Timeout
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this -> requestConnectTimeout);

		//301 und 302 folgen
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

		//Custom Header
		if (isset($this -> requestHeader) AND is_array($this -> requestHeader))
		{
			curl_setopt($curl, CURLOPT_HTTPHEADER, $this -> requestHeader);
		}

		//Sende Methode
		if ($this -> requestMethod === 'post')
		{
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_HTTPGET, FALSE);
		}
		elseif ($this -> requestMethod === 'get')
		{
			curl_setopt($curl, CURLOPT_POST, FALSE);
			curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
		}
		else
		{
			trigger_error('keine gültige Request Methode gefunden', E_USER_ERROR);
		}

		//Daten
		if (isset($this -> requestData) AND $this -> requestData != '')
		{
			if ($this -> requestMethod === 'post')
			{
				curl_setopt($curl, CURLOPT_POSTFIELDS, $this -> requestData);
			}
			else
			{
				curl_setopt($curl, CURLOPT_URL, $this -> requestApiUrl . '?' . $this -> requestData);
			}

		}

		$curl_response = curl_exec($curl);

		$this -> responseStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($this -> responseStatusCode != '200')
		{
			trigger_error("cURL HTTP Statuscode ist '" . $this -> responseStatusCode . "' für " . $this -> requestApiUrl . " :\n ", E_USER_NOTICE);
		}

		if ($errno = curl_errno($curl))
		{
			trigger_error("cURL error ({$errno}):\n ", E_USER_WARNING);
			$this -> responseCurlStatus = $errno;
		}

		if ($curl_response !== FALSE)
		{
			$this -> responseRawData = $curl_response;
			curl_close($curl);
			return TRUE;
		}
		else
		{
			//CURL Aufruf fehlgeschlagen
			trigger_error('CURL Aufruf fehlgeschlagen', E_USER_WARNING);
			curl_close($curl);
			return FALSE;
		}

	}

}
?>