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

    /**
     * @var Hackathon_IndexerStats_Model_Runtime
     */
    protected $_runtimeModel;

// Magento Hackathon Tag NEW_VAR

    protected function _construct()
    {
        $this->_runtimeModel = Mage::getModel('hackathon_indexerstats/runtime');
        parent::_construct();
    }


    public function render(Varien_Object $row)
    {
        /** @var Mage_Index_Model_Process $row */
        if ($row->getStatus() === Mage_Index_Model_Process::STATUS_RUNNING) {
            return $this->_renderProgress($row);
        } else {
            return $this->_renderAvgRuntime($row);
        }
    }

    protected function _renderAvgRuntime(Mage_Index_Model_Process $process)
    {
        return $this->_runtimeModel->getAvgRuntime($process);
    }

    protected function _renderProgress(Mage_Index_Model_Process $process)
    {
        $progress = min(100, $this->_runtimeModel->getProgress($process) * 100);
        if ($this->_runtimeModel->getProgress($process) > 1) {
            $inTimeClass = 'hackathon_indexerstats_not_in_time';
        } else {
            $inTimeClass = 'hackathon_indexerstats_in_time';
        }
        $remainingTime = $this->_runtimeModel->getRemainingTime($process);

        return <<<HTML
<div class="hackathon_indexerstats_progress {$inTimeClass}"><span style="width: {$progress}%;"><span></span></span><div>{$remainingTime}</div></div>
HTML;

    }

// Magento Hackathon Tag NEW_METHOD

}