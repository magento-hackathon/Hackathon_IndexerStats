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
        $select = $adapter->select();
        $select->from(array("history" => $this->getTable('hackathon_indexerstats/history')), array('avg_running_time' => 'AVG(running_time)'))
            ->where('process_id = :process_id');

        $bind = array(
            'process_id' => $processId,
        );

        $avgTime = $adapter->fetchOne($select, $bind);

        if (!empty($avgTime)) {
            return (int)$avgTime;
        } else {
            return 0;
        }

    }

// Magento Hackathon Tag NEW_METHOD

}