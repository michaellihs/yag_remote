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
 * Authentication controller for yag remote services
 *
 * @package Controller
 * @author Michael Knoll <mimi@kaktusteam.de>
 */
class Tx_YagRemote_Controller_AuthenticationController extends Tx_Extbase_MVC_Controller_ActionController {

    /**
     * Action for testing availability of service
     *
     * You can test this action by using the following URL:
     * <your-domain>/?eID=yagRemoteDispatcher&extensionName=YagRemote&pluginName=ajax&controllerName=Authentication&actionName=ping&pageUid=<page-uid-with-yag-remote-ts-included>
     *
     * @return string
     */
    public function pingAction() {
        var_dump($this->settings);
        return 'pong';
    }



    /**
     * Action for doing user login
     *
     * @param string user User to be logged in
     * @param password Password for user
     */
    public function loginAction($user, $password) {

    }



    public function getChallengeAction() {
        
    }

}
?>