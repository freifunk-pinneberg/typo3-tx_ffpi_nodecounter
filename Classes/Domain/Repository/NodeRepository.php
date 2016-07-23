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

    // @todo implement node role blacklist

    protected $configurationManager;

    protected $nodes;

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
    private function getNodes()
    {
        //get remote data if local empty
        if (empty($this->$nodes)) {
            $this->getJson();
        }
        return $this->nodes;
    }

    /**
     * @param mixed $nodes
     */
    private function setNodes($nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @return void
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

        $this->setNodes($data['nodes']);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $data = $this->getNodes();
        return $data;
    }

    /**
     * @return array
     */
    public function getOnline()
    {
        $online = array();
        foreach ($this->nodes as $node) {
            if ($node['status']['online'] == true) {
                $online[] = $node;
            }
        }
        return $online;
    }

    /**
     * @return array
     */
    public function getOffline()
    {
        $offline = array();
        foreach ($this->nodes as $node) {
            if ($node['status']['online'] == false) {
                $offline[] = $node;
            }
        }
        return $offline;
    }

    /**
     * @return int
     */
    public function getClientsCount()
    {

    }

    /**
     * @return int
     */
    public function getOnlineCount()
    {
        $onlineCount = count($this->getOnline());
        return $onlineCount;
    }

    /**
     * @return int
     */
    public function getOfflineCount()
    {
        $offlineCount = count($this->getOffline());
        return $offlineCount;
    }

    /**
     * @return int
     */
    public function getAllCount()
    {
        $allCount = count($this->getAll());
        return $allCount;
    }

}