<?php 

/**
 * Response
 *
 * @package CorpusClassifier
 */

class Response {

    /** @var $_content String */
    private $_content;
    
    
    /** @var $_content_type String */
    private $_content_type = 'text/html; charset=utf-8';
    
    
    /** @var $_status String */
    private $_status;
    
    
    /** @var $_headers */
    private $_headers = array ();
    
    
    /**
     * __construct
     *
     * @param $content String
     * @param $status String
     *
     * @package CorpusClassifier
     */
    public function __construct ($content=null, $status=null) {
        $this->setContent ($content);
        $this->setStatus ($status);
    }
    
    
    
    /**
     * getContentType
     *
     * @return String
     *
     * @package CorpusClassifier
     */
    public function getContentType () {
        return $this->_content_type;
    }    
    
    /**
     * setContentType
     *
     * @package CorpusClassifier
     */
    public function setContentType ($content_type) {
        $this->_content_type = $content_type;
    }    

    
    /**
     * setContent
     *
     * @package CorpusClassifier
     */
    public function setContent ($content) {
        if (is_array ($content)) {
            $this->_content = json_encode ($content, true);
        } else {
            $this->_content = $content;
        }        
        
    }
    
    
    /**
     * getStatus
     *
     * @return String
     *
     * @package CorpusClassifier
     */
    public function getStatus () {
        return $this->_status;
    }        
    
    /**
     * setStatus
     *
     * @package CorpusClassifier
     */
    public function setStatus ($status) {
        $this->_status = $status;
    }    
    
    
    /**
     * addHeader
     *
     * @param $header String
     *
     * @package CorpusClassifier
     */
    public function addHeader ($header) {
        $this->_headers[] = $header;
    }    
    
    
    /**
     * __toString
     *
     * @package CorpusClassifier
     */
    public function __toString () {
    
        // Set status
        if ($this->getStatus ()) {
            header ($_SERVER["SERVER_PROTOCOL"] . ' ' . $this->getStatus ());
        }
        
    
        // Set headers
        header ('Content-Type: ' . $this->getContentType ());
        foreach ($this->_headers as $_header) {
            header ($_header);
        } 
        
        
        // Cache headers
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {


            // set last-modified header
            header ('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
            header ('Cache-Control: public');
        }
        
        
        // Return content
        return $this->_content;
    }

}