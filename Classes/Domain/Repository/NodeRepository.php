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
    //Data
    /**
     * @var array $nodes stores all nodes
     */
    protected $nodes;

    protected $nodesOnline;

    protected $nodesOffline;

    /**
     * nodesAllCount
     *
     * @var int
     */
    protected $nodesAllCount;

    /**
     * nodesOnlineCount
     *
     * @var int
     */
    protected $nodesOnlineCount;

    /**
     * nodesOfflineCount
     *
     * @var int
     */
    protected $nodesOfflineCount;

    /**
     * clientCount
     *
     * @var int
     */
    protected $clientCount;

    //Settings
    // @todo implement node role blacklist
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
        if (empty($this->nodes)) {
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
        if (!isset($this->settings) OR empty($this->settings)) {
            throw new \RuntimeException('No Plugin Settings available', 1469348181);
        }
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
    public function getAllNodes()
    {
        $data = $this->getNodes();
        return $data;
    }

    /**
     * @return array
     */
    public function getOnlineNodes()
    {
        if (empty($this->nodesOnline)) {
            $online = array();
            foreach ($this->nodes as $node) {
                if ($node['status']['online'] == true) {
                    $online[] = $node;
                }
            }
            $this->nodesOnline = $online;
        }
        return $this->nodesOnline;
    }

    /**
     * @return array
     */
    public function getOfflineNodes()
    {
        if (empty($this->nodesOffline)) {
            $offline = array();
            foreach ($this->nodes as $node) {
                if ($node['status']['online'] == false) {
                    $offline[] = $node;
                }
            }
            $this->nodesOffline = $offline;
        }
        return $this->nodesOffline;
    }

    /**
     * @return int
     */
    public function getClientCount()
    {
        if (empty($this->clientCount)) {
            $count = 0;
            $nodes = $this->getOnlineNodes();
            foreach ($nodes as $node) {
                $count = $count + $node['status']['clients'];
            }
        }
        return $this->clientCount;
    }

    /**
     * @return int
     */
    public function getNodesOnlineCount()
    {
        if (empty($this->nodesOnlineCount)) {
            $onlineCount = count($this->getOnlineNodes());
            $this->nodesOnlineCount = $onlineCount;
        }
        return $this->nodesOnlineCount;
    }

    /**
     * @return int
     */
    public function getNodesOfflineCount()
    {
        if (empty($this->nodesOfflineCount)) {
            $offlineCount = count($this->getOfflineNodes());
            $this->nodesOfflineCount = $offlineCount;
        }
        return $this->nodesOfflineCount;
    }

    /**
     * @return int
     */
    public function getNodesAllCount()
    {
        if (empty($this->nodesAllCount)) {
            $allCount = count($this->getAllNodes());
            $this->nodesAllCount = $allCount;
        }
        return $this->nodesAllCount;
    }

}