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
        foreach ($indexer->getProcessesCollection() as $process) {
            /* @var $process Mage_Index_Model_Process */
            $this->_jsonResponse['process'][] = array(
                'code' => $process->getIndexerCode(),
                'html' => $statusRenderer->render($process));
        }
        $this->_sendJsonResponse();
    }
    /**
     * short_description_here
     * @return 
     */
    public function reindexProcessAjaxAction()
    {
        //TODO non-blocking requests for file based sessions (session_write_close() here does not seem to be enough)
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

        $this->_sendJsonResponse();
    }
    
    public function massReindexAjaxAction()
    {
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
        
        $this->_sendJsonResponse();
    }

    protected function _addError($message)
    {
    	$this->_jsonResponse['error'] = true;
    	$this->_jsonResponse['message'] = $message;
    }
    protected function _addSuccess($message)
    {
    	$this->_jsonResponse['error'] = false;
    	$this->_jsonResponse['message'] = $message;
    }
    protected function _sendJsonResponse()
    {
    	$this->getResponse()->setHeader('Content-type', 'application/json;charset=utf-8');
    	$this->getResponse()->setBody(
			Mage::helper('core')->jsonEncode($this->_jsonResponse)
    	);
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