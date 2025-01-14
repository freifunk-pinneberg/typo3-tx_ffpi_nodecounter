<?php

namespace FFPI\FfpiNodecounter\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
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
 * Node
 */
class Node extends AbstractEntity
{

    /**
     * id
     *
     * @var string
     */
    protected $id = '';

    /**
     * hostname
     *
     * @var string
     */
    protected $hostname = '';

    /**
     * role
     *
     * @var string
     */
    protected $role = '';

    /**
     * online
     *
     * @var bool
     */
    protected $online = false;

    /**
     * gateway
     *
     * @var string
     */
    protected $gateway = '';

    /**
     * clients
     *
     * @var int
     */
    protected $clients = 0;

    /**
     * Returns the id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id
     *
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the hostname
     *
     * @return string $hostname
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Sets the hostname
     *
     * @param string $hostname
     * @return void
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * Returns the role
     *
     * @return string $role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Sets the role
     *
     * @param string $role
     * @return void
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Returns the online
     *
     * @return bool $online
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * Sets the online
     *
     * @param bool $online
     * @return void
     */
    public function setOnline($online)
    {
        $this->online = $online;
    }

    /**
     * Returns the boolean state of online
     *
     * @return bool
     */
    public function isOnline()
    {
        return $this->online;
    }

    /**
     * Returns the gateway
     *
     * @return string $gateway
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Sets the gateway
     *
     * @param string $gateway
     * @return void
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Returns the clients
     *
     * @return int $clients
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Sets the clients
     *
     * @param int $clients
     * @return void
     */
    public function setClients($clients)
    {
        $this->clients = $clients;
    }

}
