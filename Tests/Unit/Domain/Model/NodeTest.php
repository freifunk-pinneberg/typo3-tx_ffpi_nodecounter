<?php

namespace FFPI\FfpiNodecounter\Tests\Unit\Domain\Model;

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
 * Test case for class \FFPI\FfpiNodecounter\Domain\Model\Node.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Kevin Quiatkowski <kevin@pinneberg.freifunk.net>
 */
class NodeTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \FFPI\FfpiNodecounter\Domain\Model\Node
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \FFPI\FfpiNodecounter\Domain\Model\Node();
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getIdReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getId()
		);
	}

	/**
	 * @test
	 */
	public function setIdForStringSetsId()
	{
		$this->subject->setId('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'id',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getHostnameReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getHostname()
		);
	}

	/**
	 * @test
	 */
	public function setHostnameForStringSetsHostname()
	{
		$this->subject->setHostname('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'hostname',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getRoleReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getRole()
		);
	}

	/**
	 * @test
	 */
	public function setRoleForStringSetsRole()
	{
		$this->subject->setRole('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'role',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getOnlineReturnsInitialValueForBool()
	{
		$this->assertSame(
			FALSE,
			$this->subject->getOnline()
		);
	}

	/**
	 * @test
	 */
	public function setOnlineForBoolSetsOnline()
	{
		$this->subject->setOnline(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'online',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getGatewayReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getGateway()
		);
	}

	/**
	 * @test
	 */
	public function setGatewayForStringSetsGateway()
	{
		$this->subject->setGateway('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'gateway',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getClientsReturnsInitialValueForInt()
	{	}

	/**
	 * @test
	 */
	public function setClientsForIntSetsClients()
	{	}
}
