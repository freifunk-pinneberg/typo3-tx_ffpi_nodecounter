<?php
namespace FFPI\FfpiNodecounter\Domain\Repository;

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

/**
 * The repository for Nodes
 */
class NodeRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    protected $configurationManager;

    protected $data;

    protected $file;

    protected $external;

    protected $settings;

    /**
     * @param $settings
     * @return void
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return mixed
     */
    private function getData()
    {
        //get remote data if local empty
        if (empty($this->data)) {
            $this->getJson();
        }
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    private function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param string $file
     * @param boolean $external
     */
    private function getJson()
    {
        $file = $this->settings['file'];
        $external = $this->settings['external']; //@todo get only external via RestAPI
        $restApi = new \FFPI\RestApi();
        $restApi->setRequestApiUrl($file);
        $restApi->setRequestMethod('get');
        $requestHeader = array('Accept: application/json');
        $restApi->setRequestHeader($requestHeader);

        $request = $restApi->sendRequest();

        $data = $restApi->getArray();

        $this->setData($data);
    }

    public function getAll()
    {
        $data = $this->getData();
        return $data;
    }

    public function getOnline()
    {
        return array();
    }

    public function getOffline()
    {
        return array();
    }
    
}