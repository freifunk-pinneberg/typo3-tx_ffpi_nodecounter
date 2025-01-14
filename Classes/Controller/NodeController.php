<?php

namespace FFPI\FfpiNodecounter\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use FFPI\FfpiNodecounter\Domain\Repository\NodeRepository;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;

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

    /**
     * nodeRepository
     *
     * @var NodeRepository
     */
    protected $nodeRepository;

    /**
     * @param NodeRepository $nodeRepository
     */
    public function injectNodeRepository(NodeRepository $nodeRepository): void
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * action count
     *
     * @return void
     */
    public function countAction(): void
    {
        $this->nodeRepository->setSettings($this->settings);

        //Get Counter data
        $counter['total'] = $this->nodeRepository->getNodesAllCount();
        $counter['offline'] = $this->nodeRepository->getNodesOfflineCount();
        $counter['online'] = $this->nodeRepository->getNodesOnlineCount();
        $counter['clients'] = $this->nodeRepository->getClientCount();

        //Assign counter to view
        $this->view->assign('counter', $counter);

    }

    public function cachedCountAction(): void
    {
        $this->countAction();
    }

    public function initializeJsonCountAction(): void
    {
        $this->defaultViewObjectName = JsonView::class;
    }

    public function jsonCountAction()
    {
        $this->nodeRepository->setSettings($this->settings);

        //Get Counter data
        $counter['total'] = $this->nodeRepository->getNodesAllCount();
        $counter['offline'] = $this->nodeRepository->getNodesOfflineCount();
        $counter['online'] = $this->nodeRepository->getNodesOnlineCount();
        $counter['clients'] = $this->nodeRepository->getClientCount();

        //Assign counter to view
        if (!$this->view instanceof JsonView) {
            throw new \RuntimeException('Expected '. JsonView::class . ', got ' . get_class($this->view));
        }
        $this->view->setVariablesToRender(['total', 'offline', 'online', 'clients']);
        $this->view->assignMultiple($counter);
    }

}
