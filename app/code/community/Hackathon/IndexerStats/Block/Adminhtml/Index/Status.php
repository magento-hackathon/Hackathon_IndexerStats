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
 * Adminhtml_Index_Status Block
 * @package Hackathon_IndexerStats
 */
class Hackathon_IndexerStats_Block_Adminhtml_Index_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

// Magento Hackathon Tag NEW_CONST

// Magento Hackathon Tag NEW_VAR

    public function render(Varien_Object $row)
    {
        return Mage::getModel('hackathon_indexerstats/runtime')
            ->getAvgRuntime($row);
    }


// Magento Hackathon Tag NEW_METHOD

}