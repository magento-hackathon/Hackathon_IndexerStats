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
 * Observer Model
 * @package Hackathon_IndexerStats
 */
class Hackathon_IndexerStats_Model_Observer extends Mage_Core_Model_Abstract
{

// Magento Hackathon Tag NEW_CONST

// Magento Hackathon Tag NEW_VAR

    /**
     * short_description_here
     * @return 
     */
    public function addIndexStatusColumn(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Template $block */
        $block = $observer->getBlock();
        if ($block instanceof Mage_Index_Block_Adminhtml_Process_Grid) {
            $this->_addIndexStatusColumnTo($block);
        }
    }
    protected function _addIndexStatusColumnTo(Mage_Index_Block_Adminhtml_Process_Grid $grid)
    {
        $grid->addColumn('status_extended', array(
            'header'    => Mage::helper('hackathon_indexerstats')->__('Status (extended)'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'status_extended',
            'renderer'  => 'hackathon_indexerstats/adminhtml_index_status',
        ));
    }

// Magento Hackathon Tag NEW_METHOD

}