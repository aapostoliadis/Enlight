<?php
/**
 * Ticket listener trac
 *
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Tests
 */
class Enlight_Components_Test_TicketListener_Trac extends PHPUnit_Extensions_TicketListener
{
	protected $serverAddress;
	protected $printTicketStateChanges;
	protected $notifyTicketStateChanges;
	
	/**
	 * Constructor method
	 * 
	 * @param string|array $options
	 */
	public function __construct($serverAddress, $printTicketStateChanges=false , $notifyTicketStateChanges=false)
    {
    	$this->serverAddress = $serverAddress;
    	$this->printTicketStateChanges = $printTicketStateChanges;
    	$this->notifyTicketStateChanges = $notifyTicketStateChanges;
    }
        
    /**
     * Get the status of a ticket message
     *
     * @param  integer $ticketId The ticket ID
     * @return array('status' => $status) ($status = new|closed|unknown_ticket)
     */
    public function getTicketInfo($ticketId = null)
    {
    	if (!is_numeric($ticketId)) {
    		return array('status' => 'invalid_ticket_id');
    	}
    	try {
    		$info = $this->getClient()->call('ticket.get' ,(int)$ticketId);
    		switch ($info[3]['status']) {
    			case 'closed':
    			case 'testing':
    			case 'testingExt':
    				return array('status' => 'closed');
    				break;
    			case 'assigned':
    			case 'new':
    			case 'reopened':
    				return array('status' => 'new');
    				break;
    			default:
    				return array('status' => 'unknown_ticket');
    		}
    	}
    	catch (Exception $e) {
    		return array('status' => 'unknown_ticket');
    	}
    }

    /**
     * Update a ticket with a new status
     *
     * @param string $ticketId   The ticket number of the ticket under test (TUT).
     * @param string $statusToBe The status of the TUT after running the associated test.
     * @param string $message    The additional message for the TUT.
     * @param string $resolution The resolution for the TUT.
     */
    protected function updateTicket($ticketId, $statusToBe, $message, $resolution)
    {
        $this->getClient()->call('ticket.update', array(
        	(int) $ticketId,
        	$message,
        	null,
        	null,
        	array(
        		'status' => $statusToBe=='closed' ? 'testing' : 'reopened',
        		'resolution' => $resolution
        	),
        	$this->notifyTicketStateChanges
        ));

        if ($this->printTicketStateChanges) {
            printf(
              "\nUpdating Trac issue #%d, status: %s\n", $ticketId, $statusToBe
            );
        }
    }

    /**
     * Returns xml rpc client
     *
     * @return Zend_XmlRpc_Client
     */
    protected function getClient()
    {
        return new Zend_XmlRpc_Client($this->serverAddress);
    }
}