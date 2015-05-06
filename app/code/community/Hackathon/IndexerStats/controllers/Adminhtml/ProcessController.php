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
 * require parent class
 */
require_once 'Mage/Index/controllers/Adminhtml/ProcessController.php';

/**
 * Adminhtml_Process Controller
 * @package Hackathon_IndexerStats
 */
class Hackathon_IndexerStats_Adminhtml_ProcessController extends Mage_Index_Adminhtml_ProcessController
{

// Magento Hackathon Tag NEW_CONST

	protected $_jsonResponse = array();

// Magento Hackathon Tag NEW_VAR

	/**
	 * AJAX action to update status column
	 * 
	 * @return
	 */
    public function statusAjaxAction()
    {        
        /* @var $indexer Mage_Index_Model_Indexer */
        $indexer    = Mage::getSingleton('index/indexer');
        /* @var $statusRenderer Hackathon_IndexerStats_Block_Adminhtml_Index_Status */
        $statusRenderer = $this->getLayout()->createBlock(
            'hackathon_indexerstats/adminhtml_index_status');
        /* @var $gridBlock Mage_Index_Block_Adminhtml_Process_Grid */
        $gridBlock = $this->getLayout()->createBlock('index/adminhtml_process_grid');
        foreach ($indexer->getProcessesCollection()->clear() as $process) {
            /* @var $process Mage_Index_Model_Process */
            $updateRequiredOptions = $process->getUpdateRequiredOptions();
            $updateRequiredDisplay = $gridBlock->decorateUpdateRequired(
                $updateRequiredOptions[intval($process->getUnprocessedEventsCollection()->count() > 0)],
                $process, null, false);
            $statusesOptions = $process->getStatusesOptions();
            $statusDisplay = $gridBlock->decorateStatus(
                $statusesOptions[$process->getStatus()],
                $process, null, false);
            $endedAtDisplay = $process->getEndedAt()
                ? Mage::helper('core')->formatDate($process->getEndedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
                : Mage::helper('index')->__('Never');
            $this->_jsonResponse['process'][] = array(
                'code'                 => $process->getIndexerCode(),
                'status'               => $process->getStatus(),
                'last_running_time'    => Mage::getModel('hackathon_indexerstats/runtime')->getLastRuntimeDisplay($process),
                'html_status'          => $statusDisplay,
                'html_update_required' => $updateRequiredDisplay,
                'html_ended_at'        => $endedAtDisplay,
                'html_time'            => $statusRenderer->render($process));
        }
        $this->_sendJsonResponse();
    }
    /**
     * Reindex process action, modified for AJAX request
     * 
     * @return 
     */
    public function reindexProcessAjaxAction()
    {
        $this->_closeSession();
        /** @var $process Mage_Index_Model_Process */
        $process = $this->_initProcess();
        if ($process) {
            try {
                Varien_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');

                $process->reindexEverything();
                Varien_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
                $this->_addSuccess(
                    Mage::helper('index')->__('%s index was rebuilt.', $process->getIndexer()->getName())
                );
            } catch (Mage_Core_Exception $e) {
                $this->_addError($e->getMessage());
            } catch (Exception $e) {
                $this->_addError(
                    Mage::helper('index')->__('There was a problem with reindexing process.')
                );
            }
        } else {
            $this->_addError(
                Mage::helper('index')->__('Cannot initialize the indexer process.')
            );
        }

        $this->statusAjaxAction();
     }
    
    /**
     * Mass reindex action, modified for AJAX request
     * 
     * @return
     */
    public function massReindexAjaxAction()
    {
        $this->_closeSession();
        /* @var $indexer Mage_Index_Model_Indexer */
        $indexer    = Mage::getSingleton('index/indexer');
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $this->_addError(Mage::helper('index')->__('Please select Indexes'));
        } else {
            try {
                $counter = 0;
                foreach ($processIds as $processId) {
                    /* @var $process Mage_Index_Model_Process */
                    $process = $indexer->getProcessById($processId);
                    if ($process && $process->getIndexer()->isVisible()) {
                        $process->reindexEverything();
                        $counter++;
                    }
                }
                $this->_addSuccess(
                        Mage::helper('index')->__('Total of %d index(es) have reindexed data.', $counter)
                );
            } catch (Mage_Core_Exception $e) {
                $this->_addError($e->getMessage());
            } catch (Exception $e) {
                $this->_addError(Mage::helper('index')->__('Cannot initialize the indexer process.'));
            }
        }
        
        $this->statusAjaxAction();
    }

    /**
     * Add error message to JSON response
     * 
     * @param string $message
     * @return $this
     */
    protected function _addError($message)
    {
    	$this->_jsonResponse['error'] = true;
    	$this->_jsonResponse['message'] = $message;
    	return $this;
    }
    /**
     * Add success message to JSON response
     * 
     * @param string $message
     * @return $this
     */
    protected function _addSuccess($message)
    {
    	$this->_jsonResponse['error'] = false;
    	$this->_jsonResponse['message'] = $message;
    	return $this;
    }
    
    /**
     * Send prepared JSON response
     * 
     * @return $this
     */
    protected function _sendJsonResponse()
    {
    	$this->getResponse()->setHeader('Content-type', 'application/json;charset=utf-8');
    	$this->getResponse()->setBody(
			Mage::helper('core')->jsonEncode($this->_jsonResponse)
    	);
    	return $this;
    }
    /**
     * Close session to unlock session storage
     * 
     * @return $this
     */
    protected function _closeSession()
    {
        session_write_close();
        unset($_SESSION);
        return $this;
    }
// Magento Hackathon Tag NEW_METHOD

    /**
     * Is allowed?
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

}