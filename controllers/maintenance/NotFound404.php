<?php

use \Zend\Diactoros\Response\JsonResponse;
use \CoreOGraphy\BaseController;

/**
 * NotFound404
 *
 * @package Allergic
 */
class NotFound404 extends BaseController {

    /**
     * handleRequest
     *
     * @package Allergic
     */
    public function handleRequest () {
        $this->_response = new JsonResponse (['message' => 'Method not found']);
        $this->_response = $this->_response->withStatus (404);
    }
}
