<?php
/**
 * This file is part of Hackathon_IndexerStats for Magento.
 *
 * @license Open Software License (OSL 3.0)
 * @author Dima Janzen <dima.janzen@gmail.com> <@dimajanzen>
 * @category Hackathon
 * @package Hackathon_IndexerStats
 * @copyright Copyright (c) 2014 Magento Hackathon (http://github.com/magento-hackathon)
 */

/**
 * Runtime Model
 * @package Hackathon_IndexerStats
 */
class Hackathon_IndexerStats_Model_Runtime extends Mage_Core_Model_Abstract
{

// Magento Hackathon Tag NEW_CONST

// Magento Hackathon Tag NEW_VAR

// Magento Hackathon Tag NEW_METHOD


    /**
     * Returns a readable string with time difference
     * copy from n98 magerun
     * @link https://github.com/netz98/n98-magerun/blob/master/src/N98/Util/DateTime.php
     *
     * @param \DateTime $time1
     * @param \DateTime $time2
     *
     * @return string
     */
    protected function _getDifferenceAsString(DateTime $datetime1, DateTime $datetime2)
    {
        if ($datetime1 == $datetime2) {
            return '0s';
        }

        $interval = date_diff($datetime1, $datetime2);
        $years = $interval->format('%y');
        $months = $interval->format('%m');
        $days = $interval->format('%d');
        $hours = $interval->format('%h');
        $minutes = $interval->format('%i');
        $seconds = $interval->format('%s');

        $differenceString = (($years) ? $years . 'Y ' : '')
            . (($months) ? $months. 'M ' : '')
            . (($days) ? $days. 'd ' : '')
            . (($hours) ? $hours. 'h ' : '')
            . (($minutes) ? $minutes . 'm ' : '')
            . (($seconds) ? $seconds . 's' : '');

        return trim($differenceString);
    }

    /**
     * Returns a readable runtime
     *
     * @param $process
     * @return mixed
     */
    public function getAvgRuntime($process)
    {
        $avgTime = Mage::getModel('hackathon_indexerstats_resource/history')
            ->getAvg($process->getId());

        $currentTime = new DateTime();
        $estimatedEndTime = new DateTime();
        $estimatedEndTime->add(new DateInterval('PT'.$avgTime.'S'));

        $avgRuntime = $this->_getDifferenceAsString($currentTime, $estimatedEndTime);
        return $avgRuntime;
    }

    public function getRemainingTime($process)
    {
        $avgTime = Mage::getModel('hackathon_indexerstats_resource/history')
            ->getAvg($process->getId());

        $currentTime = new DateTime();
        $startTime = new DateTime($process->getStartedAt());
        $estimatedEndTime = new DateTime($process->getStartedAt());
        $estimatedEndTime->add(new DateInterval('PT'.$avgTime.'S'));

        $this->_getDifferenceAsString($estimatedEndTime, $currentTime);

        return $this->_getDifferenceAsString($estimatedEndTime, $currentTime);
    }

    public function getProgress($process)
    {
        $avgTime = Mage::getModel('hackathon_indexerstats_resource/history')
            ->getAvg($process->getId());

        $currentTime = new DateTime();
        $startTime = new DateTime($process->getStartedAt());
        $estimatedEndTime = new DateTime($process->getStartedAt());
        $estimatedEndTime->add(new DateInterval('PT'.$avgTime.'S'));

        $processDiff = $currentTime->getTimestamp() - $startTime->getTimestamp();

        return $processDiff / $avgTime;
    }
}