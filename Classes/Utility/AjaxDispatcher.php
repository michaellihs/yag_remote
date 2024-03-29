<?php
/***************************************************************
* Copyright notice
*
*   2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
* All rights reserved
*
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Ajax dispatcher for handling ajax calls for yag remote service
 *
 * You can use this dispatcher with the following command URLs:
 * <your-domain>/?eID=yagRemoteDispatcher&extensionName=YagRemote&pluginName=ajax&controllerName=<yag-remote-controller>&actionName=<yag-remote-action>&pageUid=<page-uid-with-yag-remote-ts-included>
 *
 * @package Utility
 * @author Daniel Lienert <daniel@lienert.cc>
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_YagRemote_Utility_AjaxDispatcher {

	/**
	 * Array of all request Arguments
	 *
	 * @var array
	 */
	protected $requestArguments = array();



	/**
	 * Extbase Object Manager
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;



	/**
	 * @var string
	 */
	protected $extensionName;



	/**
	 * @var string
	 */
	protected $pluginName;



	/**
	 * @var string
	 */
	protected $controllerName;



	/**
	 * @var string
	 */
	protected $actionName;



	/**
	 * @var array
	 */
	protected $arguments = array();



	/**
	 * @var integer
	 */
	protected $pageUid;



    /**
     * Called by ajax.php / eID.php
     * Builds an extbase context and returns the response
     */
    public function dispatch() {
        $configuration['extensionName'] = $this->extensionName;
        $configuration['pluginName'] = $this->pluginName;

        $bootstrap = t3lib_div::makeInstance('Tx_Extbase_Core_Bootstrap');
        $bootstrap->initialize($configuration);

        $this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');

        $request = $this->buildRequest();
        $response = $this->objectManager->create('Tx_Extbase_MVC_Web_Response');

        $dispatcher =  $this->objectManager->get('Tx_Extbase_MVC_Dispatcher');
        $dispatcher->dispatch($request, $response);

        $response->sendHeaders();
        return $response->getContent();
    }



    /**
     * @param null $pageUid
     * @return Tx_PtExtbase_Utility_AjaxDispatcher
     */
    public function initTsfe() {
        global $TYPO3_CONF_VARS;

        $GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $TYPO3_CONF_VARS, $this->pageUid, '0', 1, '', '','','');
        $GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');

        return $this;
    }



	/**
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function initTypoScript() {
		$GLOBALS['TSFE']->getPageAndRootline();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		return $this;
	}



	/**
	 * @return void
	 */
    public function cleanShutDown() {
        $this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();
        $this->objectManager->get('Tx_Extbase_Reflection_Service')->shutdown();
    }



    /**
     * Build a request object
     *
     * @return Tx_Extbase_MVC_Web_Request $request
     */
    protected function buildRequest() {
        $request = $this->objectManager->get('Tx_Extbase_MVC_Web_Request'); /* @var $request Tx_Extbase_MVC_Request */
        $request->setControllerExtensionName($this->extensionName);
        $request->setPluginName($this->pluginName);
        $request->setControllerName($this->controllerName);
        $request->setControllerActionName($this->actionName);
        $request->setArguments($this->arguments);

        return $request;
    }



	/**
	 * Prepare the call arguments
     *
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
    public function initCallArguments() {
        $request = t3lib_div::_GP('request');

        if($request) {
            $this->setRequestArgumentsFromJSON($request);
        } else {
            $this->setRequestArgumentsFromGetPost();
        }

        $this->extensionName     = $this->requestArguments['extensionName'];
        $this->pluginName        = $this->requestArguments['pluginName'];
        $this->controllerName    = $this->requestArguments['controllerName'];
        $this->actionName        = $this->requestArguments['actionName'];
        $this->pageUid           = $this->requestArguments['pageUid'];

        $this->arguments         = $this->requestArguments['arguments'];
        if(!is_array($this->arguments)) $this->arguments = array();

		 return $this;
    }



    /**
     * Set the request array from JSON
     *
     * @param string $request
     */
    protected function setRequestArgumentsFromJSON($request) {
        $requestArray = json_decode($request, true);
        if(is_array($requestArray)) {
            $this->requestArguments = t3lib_div::array_merge_recursive_overrule($this->requestArguments, $requestArray);
        }
    }



    /**
     * Set the request array from the getPost array
     */
    protected function setRequestArgumentsFromGetPost() {
        $validArguments = array('extensionName','pageUid','pluginName','controllerName','actionName','arguments');
        foreach($validArguments as $argument) {
            if(t3lib_div::_GP($argument)) $this->requestArguments[$argument] = t3lib_div::_GP($argument);
        }
    }


	
	/**
	 * @param $extensionName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setExtensionName($extensionName) {
		$this->extensionName = $extensionName;
		return $this;
	}



	/**
	 * @param $pluginName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setPluginName($pluginName) {
		$this->pluginName = $pluginName;
		return $this;
	}



	/**
	 * @param $controllerName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setControllerName($controllerName) {
		$this->controllerName = $controllerName;
		return $this;
	}



	/**
	 * @param $actionName
	 * @return Tx_PtExtbase_Utility_AjaxDispatcher
	 */
	public function setActionName($actionName) {
		$this->actionName = $actionName;
		return $this;
	}

}
?>