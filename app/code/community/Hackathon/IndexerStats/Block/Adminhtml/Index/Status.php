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

    /**
     * Column renderer method
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        /** @var Mage_Index_Model_Process $row */
        if ($row->getStatus() === Mage_Index_Model_Process::STATUS_RUNNING) {
            return $this->_renderProgress($row);
        } else {
            return $this->_renderAvgRuntime($row);
        }
    }

    /**
     * Renders average runtime (for non-running process)
     *
     * @param Mage_Index_Model_Process $process
     * @return string
     */
    protected function _renderAvgRuntime(Mage_Index_Model_Process $process)
    {
        return Mage::helper('hackathon_indexerstats')->__('Average runtime:') . ' ' .
            $this->_runtimeModel->getAvgRuntime($process);
    }

    /**
     * Renders progress bar with estimated time (for running process)
     *
     * @param Mage_Index_Model_Process $process
     * @return string
     */
    protected function _renderProgress(Mage_Index_Model_Process $process)
    {
        $progressBarId = 'hackathon_indexerstats_progress_' . $process->getIndexerCode();
        $progress = min(100, $this->_runtimeModel->getProgress($process) * 100);
        if ($this->_runtimeModel->getProgress($process) >= 1) {
            $inTimeClass = 'hackathon_indexerstats_not_in_time';
            $timeCaption = Mage::helper('hackathon_indexerstats')->__('over time');
        } else {
            $inTimeClass = 'hackathon_indexerstats_in_time';
            $timeCaption = Mage::helper('hackathon_indexerstats')->__('remaining');
        }
        $now = date_create()->getTimestamp();
        $startTimestamp = $this->_runtimeModel->getStartTime($process)->getTimestamp();
        $estimatedEndTimestamp = $this->_runtimeModel->getEstimatedEndTime($process)->getTimestamp();
        $timeDisplay = $this->_runtimeModel->getRemainingTime($process);

        return <<<HTML
<div id="{$progressBarId}" data-now="{$now}" data-started="{$startTimestamp}" data-estimated_end="{$estimatedEndTimestamp}" class="hackathon_indexerstats_progress {$inTimeClass}">
    <span style="width: {$progress}%;"><span></span></span>
    <div><span class="hackathon_indexerstats_time_display">{$timeDisplay}</span> <span class="hackathon_indexerstats_time_caption">{$timeCaption}</span></div>
</div>
HTML;

    }

// Magento Hackathon Tag NEW_METHOD

}