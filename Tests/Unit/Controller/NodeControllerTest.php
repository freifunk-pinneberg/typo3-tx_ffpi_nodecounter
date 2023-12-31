<?php
namespace FFPI\FfpiNodecounter\Tests\Unit\Controller;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use FFPI\FfpiNodecounter\Controller\NodeController;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Kevin Quiatkowski <kevin@pinneberg.freifunk.net>
 *  			
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class FFPI\FfpiNodecounter\Controller\NodeController.
 *
 * @author Kevin Quiatkowski <kevin@pinneberg.freifunk.net>
 */
class NodeControllerTest extends UnitTestCase
{

	/**
	 * @var NodeController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('FFPI\\FfpiNodecounter\\Controller\\NodeController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

}
