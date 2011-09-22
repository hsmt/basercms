<?php
/* SVN FILE: $Id$ */
/**
 * ErrorHandler 拡張クラス
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Core', 'Error');
/**
 * ErrorHandler 拡張クラス
 * @package baser
 */
class BaserAppError extends ErrorHandler {
/**
 * Class constructor.
 *
 * @param string $method Method producing the error
 * @param array $messages Error messages
 */
	function __construct($method, $messages) {
		App::import('Core', 'Sanitize');
		static $__previousError = null;

		if ($__previousError != array($method, $messages)) {
			$__previousError = array($method, $messages);
			$this->controller =& new CakeErrorController();
		} else {
			$this->controller =& new Controller();
			$this->controller->viewPath = 'errors';
		}

		$options = array('escape' => false);
		$messages = Sanitize::clean($messages, $options);

		if (!isset($messages[0])) {
			$messages = array($messages);
		}

		if (method_exists($this->controller, 'apperror')) {
			return $this->controller->appError($method, $messages);
		}

		if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($this)))) {
			$method = 'error';
		}

		if ($method !== 'error') {
			if (Configure::read() == 0) {
				$method = 'error404';
				if (isset($code) && $code == 500) {
					$method = 'error500';
				}
			}
		}
		
		// >>> CUSTOMIZE MODIFY 2011/08/19 ryuring
		//$this->dispatchMethod($method, $messages);
		//$this->_stop();
		// ---
		if(!isset($this->controller->params['return'])) {
			$this->dispatchMethod($method, $messages);
			$this->_stop();
		} else {
			return;
		}
		// <<<
		
	}
/**
 * クラスが見つからない
 * @param array $params
 */
	function missingClass($params) {
		if($params['className']) {
			$this->controller->set('className',$params['className']);
		}
		if($params['notice']) {
			$this->controller->set('notice', $params['notice']);
		}
		$this->_outputMessage('missing_class');
	}
	
}
?>