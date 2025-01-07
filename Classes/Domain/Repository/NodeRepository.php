<?php

namespace FFPI\FfpiNodecounter\Domain\Repository;

use FFPI\FfpiNodecounter\Domain\Model\Node;
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
 * @extends Repository<Node>
 */
class NodeRepository extends Repository
{
    const CACHE_NAME = 'nodes';

    //Data
    /**
     * @var array<mixed> $nodes stores all nodes
     */
    protected $nodes = [];

    /**
     * @var array<mixed>
     */
    protected $nodesOnline = [];

    /**
     * @var array<mixed>
     */
    protected $nodesOffline = [];

    /**
     * nodesAllCount
     *
     * @var int
     */
    protected $nodesAllCount = 0;

    /**
     * nodesOnlineCount
     *
     * @var int
     */
    protected $nodesOnlineCount = 0;

    /**
     * nodesOfflineCount
     *
     * @var int
     */
    protected $nodesOfflineCount = 0;

    /**
     * clientCount
     *
     * @var int
     */
    protected $clientCount = 0;

    /**
     * @var array<mixed>|null
     */
    protected $settings;


    /**
     * @param array<mixed> $settings
     * @return void
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array<mixed>
     */
    private function getNodes(): array
    {
        //get remote data if local empty
        if (empty($this->nodes)) {
            $this->getJson();
        }
        return $this->nodes;
    }

    /**
     * @param array<mixed> $nodes
     */
    private function setNodes($nodes): void
    {
        $this->nodes = $nodes;
    }

    /**
     * @return array<mixed>
     */
    private function getNodesFromApi(): array
    {
        $file = $this->settings['nodeListFile'];
        $external = $this->settings['nodeListExternal'];

        if (!$external) {
            // Verwende fopen, wenn external auf false gesetzt ist
            if (!file_exists($file) || !is_readable($file)) {
                throw new \RuntimeException('The local nodelist file is not readable or dosen\'t exists: ' . $file);
            }

            $fileContent = file_get_contents($file);
            if ($fileContent) {
                $data = json_decode($fileContent, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('Error while decoding the json: ' . json_last_error_msg());
                }
            } else {
                throw new \RuntimeException('Something went wrong reading the file: ' . $file);
            }

        } else {
            $restApi = new RestApi();
            $restApi->setRequestApiUrl($file);
            $restApi->setRequestMethod('get');
            $requestHeader = ['Accept: application/json'];
            $restApi->setRequestHeader($requestHeader);

            $request = $restApi->sendRequest();
            $data = $restApi->getArray();
        }
        return $data;
    }

    /**
     * @return array<mixed>|null
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
    private function saveNodesCache(array $nodes): void
    {
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('ffpi_nodecounter_result');

        $nodes['cacheTime'] = time();
        // Save value in cache
        $cache->set($this->getCacheName(), $nodes, [], 0);
    }

    /**
     * @return string
     */
    protected function getCacheName(): string
    {
        return (string) crc32(self::CACHE_NAME . $this->settings['nodeListFile']);
    }

    /**
     * @return void
     */
    private function getJson(): void
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
            if (is_array($apiData) && !empty($apiData)) {
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
     * @return array<mixed>
     */
    public function getAllNodes(): array
    {
        $data = $this->getNodes();
        if (!$data) {
            $data = [];
        }
        return $data;
    }

    /**
     * @return array<mixed>
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
     * @return array<mixed>
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
    public function getClientCount(): int
    {
        if (empty($this->clientCount)) {
            $count = 0;
            $nodes = $this->getOnlineNodes();
            foreach ($nodes as $node) {
                $count = $count + (int) $node['statistics']['clients'];
            }
            $this->clientCount = $count;
        }
        return $this->clientCount;
    }

    /**
     * @return int
     */
    public function getNodesOnlineCount(): int
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
    public function getNodesAllCount(): int
    {
        if (empty($this->nodesAllCount)) {
            $allCount = count($this->getAllNodes());
            $this->nodesAllCount = $allCount;
        }
        return $this->nodesAllCount;
    }

}
