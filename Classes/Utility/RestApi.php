<?php

namespace FFPI\FfpiNodecounter\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Kevin Quiatkowski <kevin@pinneberg.freifunk.net>
 *
 *  All rights reserved
 *
 *  You may use, distribute and modify this code under the
 *  terms of the GNU General Public License Version 3
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class RestApi
{
    /** @var string */
    protected $requestApiUrl;
    /** @var string */
    protected $requestData;
    /** @var array */
    protected $requestHeader;
    protected $requestMethod;
    protected $requestConnectTimeout;

    /** @var string */
    protected $responseRawData;
    /** @var string */
    protected $responseJsonData;
    /** @var array */
    protected $responseArrayData;
    protected $responseStatusCode;
    protected $responseCurlStatus;

    public function __construct()
    {
        $this->setRequestMethod();
        $this->setRequestConnectTimeout();
    }

    /*
     * Prüfen ob es sich ums JSON Format handelt.
     *
     * @return boolean
     */
    private function isJson(string $string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param string $requestApiUrl
     * @return void
     */
    public function setRequestApiUrl(string $requestApiUrl = 'http://localhost'): void
    {
        $this->requestApiUrl = $requestApiUrl;
    }

    /**
     * @param array $dataArray
     * @return void
     */
    public function setRequestData(array $dataArray): void
    {
        $i = 0;
        $dataString = '';
        foreach ($dataArray as $key => $value) {
            $tempDataString = $key . '=' . $value;
            if ($i == 0) {
                $dataString = $tempDataString;
            } else {
                $dataString .= '&' . $tempDataString;
            }
            $i++;
        }
        $this->requestData = $dataString;
    }

    public function setRequestHeader(array $requestHeader): void
    {
        $this->requestHeader = $requestHeader;
    }

    /**
     * setRequestMethod
     *
     * @param string $requestMethod 'get' oder 'post'
     * @return void
     */
    public function setRequestMethod(string $requestMethod = 'get'): void
    {
        $this->requestMethod = strtolower($requestMethod);
    }

    /**
     * @param int $timeout
     * @return void
     */
    public function setRequestConnectTimeout(int $timeout = 5): void
    {
        $this->requestConnectTimeout = $timeout;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->responseRawData;
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        $rawData = $this->getRawData();
        if (is_string($rawData) && $this->isJson($rawData)) {
            return $rawData;
        } else {
            trigger_error('No valid json from' . $this->requestApiUrl, E_USER_WARNING);
            //@TODO: Eventuell auf andere Formate prüfen und zu json umwandeln
            return '{}';
        }
    }

    public function getArray()
    {
        $json = $this->getJson();
        $array = json_decode($json, true);
        return $array;
    }

    public function getStatusCode()
    {
        return $this->responseStatusCode;
    }

    public function getCurlStatus()
    {
        return $this->responseCurlStatus ?? null;
    }

    public function sendRequest()
    {
        if (!isset($this->requestApiUrl) or $this->requestApiUrl == '') {
            trigger_error('no API URL', E_USER_ERROR);
            return false;
        }

        if (!isset($this->requestMethod) or $this->requestMethod == '') {
            $this->setRequestMethod();
        }

        $curl = curl_init($this->requestApiUrl);
        if($curl === false){
            trigger_error('Could not initalize curl with url ' . $this->requestApiUrl, E_USER_ERROR);
        }
        //Im Erfolgsfall nicht TRUE sondern Daten zurückliefern
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //Useragent
        curl_setopt($curl, CURLOPT_USERAGENT, 'TYPO3 at ' . $_SERVER['HTTP_HOST']);

        //Timeout
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->requestConnectTimeout);

        //301 und 302 folgen
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        //Custom Header
        if (isset($this->requestHeader) && is_array($this->requestHeader)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->requestHeader);
        }

        //Sende Methode
        if ($this->requestMethod === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPGET, false);
        } elseif ($this->requestMethod === 'get') {
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        } else {
            trigger_error('keine gültige Request Methode gefunden', E_USER_ERROR);
        }

        //Daten
        if (isset($this->requestData) and $this->requestData != '') {
            if ($this->requestMethod === 'post') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->requestData);
            } else {
                curl_setopt($curl, CURLOPT_URL, $this->requestApiUrl . '?' . $this->requestData);
            }

        }

        $curl_response = curl_exec($curl);

        $this->responseStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($this->responseStatusCode != '200') {
            trigger_error("cURL HTTP Statuscode was '" . $this->responseStatusCode . "' for " . $this->requestApiUrl . " :\n ",
                E_USER_NOTICE);
        }

        if ($errno = curl_errno($curl)) {
            trigger_error("cURL error ({$errno}):\n ", E_USER_WARNING);
            $this->responseCurlStatus = $errno;
        }

        if ($curl_response !== false) {
            $this->responseRawData = $curl_response;
            curl_close($curl);
            return true;
        } else {
            //CURL Aufruf fehlgeschlagen
            trigger_error('CURL request failed', E_USER_WARNING);
            curl_close($curl);
            return false;
        }

    }
}
