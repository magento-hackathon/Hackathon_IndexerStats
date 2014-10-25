<?php
/**
 * This file is part of Hackathon_IndexerStats for Magento.
 *
 * @license Open Software License (OSL 3.0)
 * @author Fabian Schmengler <fs@integer-net.de> <@fschmengler>
 * @category Hackathon
 * @package Hackathon_IndexerStats
 * @copyright Copyright (c) 2014 Magento Hackathon (http://github.com/magento-hackathon)
 */

/**
 * History Model
 * @package Hackathon_IndexerStats
 */
class Hackathon_IndexerStats_Model_History extends Mage_Core_Model_Abstract
{

// Magento Hackathon Tag NEW_CONST

// Magento Hackathon Tag NEW_VAR

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'history';

    /**
     * Parameter name in event
     * In observe method you can use $observer->getEvent()->getObject() in this case
     * @var string
     */
    protected $_eventObject = 'history';

    /**
     * History Constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('hackathon_indexerstats/history');
    }

// Magento Hackathon Tag NEW_METHOD

}