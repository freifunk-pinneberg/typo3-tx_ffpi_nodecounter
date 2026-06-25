<?php

namespace FFPI\FfpiNodecounter\Controller;

use FFPI\FfpiNodecounter\Domain\Repository\NodeRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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
 * NodeController
 */
class NodeController extends ActionController
{
    public function __construct(
        protected readonly NodeRepository $nodeRepository,
    ) {
    }

    public function countAction(): ResponseInterface
    {
        $this->view->assign('counter', $this->buildCounter());

        return $this->htmlResponse();
    }

    public function cachedCountAction(): ResponseInterface
    {
        return $this->countAction();
    }

    public function jsonCountAction(): ResponseInterface
    {
        return $this->jsonResponse((string)json_encode($this->buildCounter(), JSON_THROW_ON_ERROR));
    }

    /**
     * @return array{total: int, offline: int, online: int, clients: int}
     */
    private function buildCounter(): array
    {
        $this->nodeRepository->setSettings($this->settings);

        return [
            'total' => $this->nodeRepository->getNodesAllCount(),
            'offline' => $this->nodeRepository->getNodesOfflineCount(),
            'online' => $this->nodeRepository->getNodesOnlineCount(),
            'clients' => $this->nodeRepository->getClientCount(),
        ];
    }
}
