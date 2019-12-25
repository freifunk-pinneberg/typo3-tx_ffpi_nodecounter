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
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


class RestApi
{
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
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function setRequestApiUrl($requestApiUrl = 'http://localhost')
    {
        return $this->requestApiUrl = $requestApiUrl;
    }

    public function setRequestData($dataArray)
    {
        if (!is_array($dataArray)) {
            trigger_error('$dataArray is not an raay', E_USER_ERROR);
            return false;
        }

        $i = 0;
        foreach ($dataArray as $key => $value) {
            $dataString = '';
            $tempDataString = $key . '=' . $value;
            if ($i = 0) {
                $dataString = $tempDataString;
            } else {
                $dataString .= '&' . $tempDataString;
            }
            $i++;
        }
        return $this->requestData = $dataString;
    }

    public function setRequestHeader($requestHeader)
    {
        if (is_array($requestHeader)) {
            return $this->requestHeader = $requestHeader;
        } else {
            trigger_error('Request Header could not be set', E_USER_WARNING);
            return false;
        }

    }

    /**
     * setRequestMethod
     *
     * @param string $requestMethod 'get' oder 'post'
     * @return boolean
     */
    public function setRequestMethod($requestMethod = 'get')
    {
        return $this->requestMethod = strtolower($requestMethod);
    }

    public function setRequestConnectTimeout($timeout = '5')
    {
        if (is_numeric($timeout) AND $timeout >= 0) {
            $this->requestConnectTimeout = $timeout;
        }
    }

    public function getRawData()
    {
        return $this->responseRawData;
    }

    public function getJson()
    {
        $rawData = $this->getRawData();
        if ($this->isJson($rawData)) {
            return $rawData;
        } else {
            //@TODO: Eventuell auf andere Formate prüfen und zu json umwandeln
            return false;
        }
    }

    public function getArray()
    {
        $json = $this->getJson();
        if ($json !== false) {
            $array = json_decode($json, true);
            return $array;
        } else {
            return false;
        }

    }

    public function getStatusCode()
    {
        return $this->responseStatusCode;
    }

    public function getCurlStatus()
    {
        if (isset($this->responseCurlStatus)) {
            return $this->responseCurlStatus;
        } else {
            return null;
        }
    }

    public function sendRequest()
    {
        if (!isset($this->requestApiUrl) OR $this->requestApiUrl == '') {
            trigger_error('no API URL', E_USER_ERROR);
            return false;
        }

        if (!isset($this->requestMethod) OR $this->requestMethod == '') {
            $this->setRequestMethod();
        }

        $curl = curl_init($this->requestApiUrl);
        //Im Erfolgsfall nicht TRUE sondern Daten zurückliefern
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //Useragent
        curl_setopt($curl, CURLOPT_USERAGENT, 'TYPO3 at ' . $_SERVER['HTTP_HOST']);

        //Timeout
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->requestConnectTimeout);

        //301 und 302 folgen
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        //Custom Header
        if (isset($this->requestHeader) AND is_array($this->requestHeader)) {
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
        if (isset($this->requestData) AND $this->requestData != '') {
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