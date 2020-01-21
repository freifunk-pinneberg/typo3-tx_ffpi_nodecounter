<?php

namespace FFPI\FfpiNodecounter\Domain\Repository;

use FFPI\FfpiNodecounter\Utility\RestApi;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
    const CACHE_NAME = 'nodes';

    //Data
    /**
     * @var array $nodes stores all nodes
     */
    protected $nodes = [];

    /**
     * @var array
     */
    protected $nodesOnline = [];

    /**
     * @var array
     */
    protected $nodesOffline = [];

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
     * @return array|null
     */
    private function getCachedNodes(): ?array
    {
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('ffpi_nodecounter_result');

        $entry = $cache->get(self::CACHE_NAME);
        if(!is_array($entry)){
            return null;
        }
        return $entry;
    }

    /**
     * @param array $nodes
     */
    private function saveNodesCache(array $nodes)
    {
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('ffpi_nodecounter_result');

        // Save value in cache
        $cache->set(self::CACHE_NAME, $nodes, [], 60);
    }

    /**
     * @return void
     */
    private function getJson()
    {
        if (!isset($this->settings) OR empty($this->settings)) {
            throw new \RuntimeException('No Plugin Settings available', 1469348181);
        }

        $cachedNodes = $this->getCachedNodes();
        if(is_array($cachedNodes)){
            $this->setNodes($cachedNodes);
        } else {
            $file = $this->settings['nodeListFile'];
            $external = $this->settings['nodeListExternal']; //@todo get only external via RestAPI
            $restApi = new RestApi();
            $restApi->setRequestApiUrl($file);
            $restApi->setRequestMethod('get');
            $requestHeader = array('Accept: application/json');
            $restApi->setRequestHeader($requestHeader);

            $request = $restApi->sendRequest();

            $data = $restApi->getArray();

            $this->setNodes($data['nodes']);
            $this->saveNodesCache($data['nodes']);
        }


    }

    /**
     * @return array
     */
    public function getAllNodes()
    {
        $data = $this->getNodes();
        if (!$data) {
            $data = [];
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getOnlineNodes(): array
    {
        if (empty($this->nodesOnline)) {
            $online = array();
            foreach ((array)$this->nodes as $node) {
                if ($node['flags']['online'] == true) {
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
    public function getOfflineNodes(): array
    {
        if (empty($this->nodesOffline)) {
            $offline = array();
            foreach ((array)$this->nodes as $node) {
                if ($node['flags']['online'] == false) {
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
                $count = $count + $node['statistics']['clients'];
            }
            $this->clientCount = $count;
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
    public function getNodesOfflineCount(): int
    {
        if (empty($this->nodesOfflineCount)) {
            $offlineCount = count($this->getOfflineNodes());
            $this->nodesOfflineCount = $offlineCount;
        }
        return (int)$this->nodesOfflineCount;
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