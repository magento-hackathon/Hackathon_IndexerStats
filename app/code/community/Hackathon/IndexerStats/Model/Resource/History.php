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
 * Resource Model of History
 * @package Hackathon_IndexerStats
 */
class Hackathon_IndexerStats_Model_Resource_History extends Mage_Core_Model_Resource_Db_Abstract
{

    const XML_PATH_NUMBER_FOR_AVG = 'hackathon_indexerstats/process/number_for_avg';
// Magento Hackathon Tag NEW_CONST

// Magento Hackathon Tag NEW_VAR

    /**
     * History Resource Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('hackathon_indexerstats/history', 'history_id');
    }

    public function getAvg($processId)
    {
        $adapter = $this->_getReadAdapter();

        $selectLastEntries = $adapter->select()
            ->from(
                array("history" => $this->getTable('hackathon_indexerstats/history')),
                array('running_time' => 'running_time'))
            ->where('process_id = :process_id')
            ->order('history_id DESC')
            ->limit($this->_getNumberOfRowsUsedForAverage());

        $selectAverage = $adapter->select();
        $selectAverage->from(
            array("dataset" => new Zend_Db_Expr("($selectLastEntries)")),
            array('avg_running_time' => 'AVG(dataset.running_time)'));

        Mage::log($selectAverage->__toString());
        $bind = array(
            'process_id' => $processId,
        );

        $avgTime = $adapter->fetchOne($selectAverage, $bind);

        if (!empty($avgTime)) {
            return (int)$avgTime;
        } else {
            return 0;
        }
    }

    /**
     * Returns how many history rows should be used to calculate the estimated time
     *
     * @return int
     */
    protected function _getNumberOfRowsUsedForAverage()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_NUMBER_FOR_AVG);
    }

// Magento Hackathon Tag NEW_METHOD

}