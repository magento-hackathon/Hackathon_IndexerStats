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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var Mage_Index_Model_Resource_Process_Collection $indexProcessCollection */
$indexProcessCollection = Mage::getResourceModel('index/process_collection');

/** @var Mage_Index_Model_Process $indexProcess */
foreach ($indexProcessCollection as $indexProcess) {
    /** @var Hackathon_IndexerStats_Model_History $processHistory */
    $processHistory = Mage::getModel('hackathon_indexerstats/history');
    $processHistory->setDataFromProcess($indexProcess)->save();
}
$installer->endSetup();

