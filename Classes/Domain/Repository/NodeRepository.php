<?php

namespace FFPI\FfpiNodecounter\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
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
 *  You may use, distribute and modify this code under the
 *  terms of the GNU General Public License Version 3
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * The repository for Nodes
 */
class NodeRepository extends Repository
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

    /**
     * @var array|null
     */
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
    private function getNodesFromApi(): ?array
    {
        $file = $this->settings['nodeListFile'];
        $external = $this->settings['nodeListExternal']; //@todo get only external via RestAPI
        $restApi = new RestApi();
        $restApi->setRequestApiUrl($file);
        $restApi->setRequestMethod('get');
        $requestHeader = ['Accept: application/json'];
        $restApi->setRequestHeader($requestHeader);

        $request = $restApi->sendRequest();

        $data = $restApi->getArray();
        if (is_array($data)) {
            return $data;
        } else {
            return null;
        }
    }

    /**
     * @return array|null
     */
    private function getCachedNodes(): ?array
    {
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('ffpi_nodecounter_result');

        $entry = $cache->get($this->getCacheName());
        if (!is_array($entry)) {
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

        $nodes['cacheTime'] = time();
        // Save value in cache
        $cache->set($this->getCacheName(), $nodes, [], 0);
    }

    /**
     * @return int
     */
    protected function getCacheName(): int
    {
        return crc32(self::CACHE_NAME . $this->settings['nodeListFile']);
    }

    /**
     * @return void
     */
    private function getJson()
    {
        if (!isset($this->settings) or empty($this->settings)) {
            throw new \RuntimeException('No Plugin Settings available', 1469348181);
        }

        $cachedData = $this->getCachedNodes();
        $age = time();
        if (is_array($cachedData)) {
            $cachedTime = $cachedData['cacheTime'];
            $age = $age - $cachedTime;
        }
        if (!is_array($cachedData) || $age > 90) {
            //Cache leer order abgelaufen.
            $apiData = $this->getNodesFromApi();
            if (is_array($apiData)) {
                $this->saveNodesCache($apiData);
                $this->setNodes($apiData['nodes']);
            } elseif (is_array($cachedData)) {
                $this->setNodes($cachedData['nodes']);
            } else {
                $this->setNodes([]);
            }
        } else {
            $this->setNodes($cachedData['nodes']);
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
            $online = [];
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
            $offline = [];
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
