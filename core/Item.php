<?php

namespace CoreOGraphy;

/**
 * Item
 *
 * Simple Active Record Pattern
 *
 * @package Core-o-Graphy
 */
class Item {
    
    /** @var $_data */
    protected $_data = array ();
    
    
    /** @var $_table */
    protected $_table;
    
    
    /** @var $_connection Database */
    protected $_connection;
    
    
    /**
     * __construct
     *
     * @param $data int|array
     * @param $table
     *
     * @package Core-o-Graphy
     */
    public function __construct ($data = array (), $table) {
    
        // Get database connection
        global $container;
        $this->_connection = $container['connection'];
        
        
        // Assign table
        $this->_table = $table;
        
        
        // An array?
        if (is_array ($data)) {

            $this->_data = $data;
        
        } else {
            
            // Prepare SQL statement
            $sql = "SELECT * FROM " . $table . " WHERE id = :id";
            
            
            // Run the query
            $this->_connection->prepare ($sql, array(':id' => $data));
            $data = $this->_connection->execute ();
            
            
            // Bind data
            $this->_data = reset ($data);
            
        }
        
    }
    
    
    /**
     * getTable
     *
     * @package Core-o-Graphy
     */
    public function getTable () {
        return $this->_table;
    }
    
    
    /**
     * get
     *
     * @package Core-o-Graphy
     */
    public function get ($value) {
        return isset ($this->_data[$value]) ? $this->_data[$value] : null;
    }
    
    
    /**
     * __get
     *
     * @package Core-o-Graphy
     */
    public function __get ($value) {
        return $this->get ($value);
    }
    
    
    /**
     * __set
     *
     * @package Core-o-Graphy
     */
     
    public function __set ($name, $value) {
    
        /** @var $key String The field without "set" */
        $key = preg_replace ('/^set/', '', $name);
    
        
        // Convert to lowercase
        $key = mb_strtolower ($key);
        
        
        // Store value
        $this->_data[$key] = $value;
    }
    
    
    /**
     * store
     *
     * @param $on_duplicate_key String|null
     *
     * @package Core-o-Graphy
     */    
    public function store ($on_duplicate_key=null) {
        
        // Update
        if (isset ($this->_data['created_at'])) {
            $this->_connection->update ($this->getTable (), $this->_data);
        
        // Insert
        } else {
        
            $id = $this->_connection->insert ($this->getTable (), $this->_data, $on_duplicate_key);
            
            if ($id) {
                $this->_data['id'] = $id;
                $this->_data['created_at'] = date ('Y-m-d H:i:s');
            }
        }
    }
    
    
    /**
     * remove
     *
     * @package Core-o-Graphy
     */    
    public function remove () {
        $this->_connection->remove ($this->getTable (), $this->getId ());
    }    
    
    
    /**
     * getArray
     *
     * @param $extra Array
     * @param $exclude Array
     *
     * @package Core-o-Graphy
     */
    public function __toString () {
        return $this->toJSON ();
    }    
    
    /**
     * getArray
     *
     * @param $extra Array
     * @param $exclude Array
     *
     * @package Core-o-Graphy
     */
    public function getArray ($extra=array (), $exclude=array ()) {
        return array_diff_key (array_merge ($this->_data, $extra), array_flip ($exclude));
    }
    
    
    /**
     * toJSON
     *
     * @package Core-o-Graphy
     */
    public function toJSON () {
        return json_encode ($this->_data);
    }
}