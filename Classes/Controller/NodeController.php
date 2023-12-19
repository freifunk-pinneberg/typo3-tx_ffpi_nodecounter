<?php

namespace FFPI\FfpiNodecounter\Controller;

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
class NodeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * nodeRepository
     *
     * @var \FFPI\FfpiNodecounter\Domain\Repository\NodeRepository
     *
     */
    protected $nodeRepository;

    /**
     * @param \FFPI\FfpiNodecounter\Domain\Repository\NodeRepository $nodeRepository
     */
    public function injectNodeRepository(\FFPI\FfpiNodecounter\Domain\Repository\NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * action count
     *
     * @return void
     */
    public function countAction()
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

}
