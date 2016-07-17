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
 * NodeController
 */
class NodeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * nodeRepository
     *
     * @var \FFPI\FfpiNodecounter\Domain\Repository\NodeRepository
     * @inject
     */
    protected $nodeRepository = NULL;
    
    /**
     * action count
     *
     * @return void
     */
    public function countAction()
    {
        $nodes = $this->nodeRepository->getAll();
        var_dump($this->settings);
        $this->view->assign('settings', $this->settings);
        $this->view->assign('nodes', $nodes);
        
    }

}