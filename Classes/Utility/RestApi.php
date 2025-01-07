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

    /** @var array<string> */
    protected $requestHeader;

    /** @var string */
    protected $requestMethod;

    /** @var int */
    protected $requestConnectTimeout;

    /** @var string */
    protected $responseRawData;

    /** @var string */
    protected $responseJsonData;

    /** @var array<mixed> */
    protected $responseArrayData;

    /** @var int|null */
    protected $responseStatusCode;

    /** @var int|null */
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

    /**
     * @return array<mixed>
     */
    public function getArray(): array
    {
        $json = $this->getJson();
        $array = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Error while decoding the json: ' . json_last_error_msg());
        }
        return $array;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->responseStatusCode;
    }

    /**
     * @return int|null
     */
    public function getCurlStatus()
    {
        return $this->responseCurlStatus ?? null;
    }

    /**
     * @return bool
     */
    public function sendRequest(): bool
    {
        if (empty($this->requestApiUrl)) {
            trigger_error('no API URL, make sure you set one in plugin.tx_ffpinodecounter.settings.nodeListFile', E_USER_ERROR);
        }

        if (empty($this->requestMethod)) {
            $this->setRequestMethod();
        }

        $curl = curl_init($this->requestApiUrl);
        if($curl === false){
            trigger_error('Could not initialize curl with url ' . $this->requestApiUrl, E_USER_ERROR);
        }
        //In case of success, get the actual data and not just true
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //We want to set a User-Agent
        $host = preg_replace('/[^a-zA-Z0-9.-]/', '', $_SERVER['HTTP_HOST']);
        curl_setopt($curl, CURLOPT_USERAGENT, 'TYPO3 at ' . $host);

        //Timeout
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->requestConnectTimeout);

        //Follow 301 and 302 redirects
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        //Custom Header
        if (!empty($this->requestHeader)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->requestHeader);
        }

        //set request method
        if ($this->requestMethod === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPGET, false);
        } elseif ($this->requestMethod === 'get') {
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        } else {
            trigger_error('No valid request method found', E_USER_ERROR);
        }

        //If we have to attach some data
        if (!empty($this->requestData)) {
            if ($this->requestMethod === 'post') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->requestData);
            } else {
                curl_setopt($curl, CURLOPT_URL, $this->requestApiUrl . '?' . $this->requestData);
            }
        }

        /** @var string|false $curl_response can't be true as we set CURLOPT_RETURNTRANSFER earlier */
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
