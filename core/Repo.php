<?php

namespace CoreOGraphy;

/**
 * Repo
 *
 * This class represents the base repository to extract information
 * for all the custom classes
 *
 * It's need to define a _class and a _table
 *
 * Supports methods to:
 * - Retrieve a single instance by ID
 * - Retrieve all the instances (paged)
 *
 * @package Core-o-Graphy
 */

abstract class Repo {
    
    /** $_class String */
    protected $_class;
    
    
    /** $_table String */
    protected $_table;
    
    
    /** $_connection Database */
    protected $_connection;
    
    
    /** 
     * __construct
     *
     * @package Core-o-Graphy
     */
    
    public function __construct () {
        global $container;
        $this->_connection = $container['connection'];
    }
    
    
    /**
     * truncate
     *
     * @package Core-o-Graphy
     */
    public function truncate () {
        $this->_connection->truncate ($this->_table);        
    }    
    
    
    /**
     * getById
     *
     * @param $id int
     *
     * @package Core-o-Graphy
     */
    public function getById ($id) {
        return new $this->_class ($id);
    }
    
    
    /**
     * store
     *
     * @param $items Array|Object
     * @param $on_duplicate_key String
     * @param $chunk_size int 
     *
     * @package Core-o-Graphy
     */
    
    public function store ($items, $on_duplicate_key = null, $chunk_size = 100) {
    
        // Prepare item as an array
        if (is_object ($items)) {
            $items = [$items];
        }
        
        
        /** @var $first_item Object Get first element */
        $first_item = reset ($items);
        
        
        /** @var $field_names Array */
        $field_names = array_keys ($first_item->getArray ());
        
        
        
        /** @var $sql_on_duplicate_key String */
        $sql_on_duplicate_key = ' ON DUPLICATE KEY UPDATE id=id, updated_at=NOW()';
    
        
        // Attach user conditions
        if ($on_duplicate_key) {
            $sql_on_duplicate_key .= ', ' . $on_duplicate_key;
        }

        
        /** @var $chunks Array To avoid big */
        $chunks = array_chunk ($items,  $chunk_size);
        
        
        /** @var $sql String The base String */
        $sql = "INSERT INTO " . $this->_table . " (" . implode (',', $field_names) . ") VALUES ";
        
        
        
        // Chunks
        foreach ($chunks as $items) {
        
            /** @var $sql_values Array */
            $sql_values = [];
            
            
            // Foreach item in the chunked array
            foreach ($items as $item) {
                
                /** @var $data Array */
                $data = $item->getArray ();
                
                
                // Attach values for each item
                $sql_values[] = "(\"" . implode ('", "', array_values ($data)) . "\")";
                
                
                
            }
            
            
            /** $sql_for_this_chunk String The SQL ensembled */
            $sql_for_this_chunk = $sql . implode (',', $sql_values) . $sql_on_duplicate_key;
            
            
            // Run connection
            $this->_connection->prepare ($sql_for_this_chunk, []);
            $results = $this->_connection->execute ();            
            
        
        }
    
    }
    


    /**
     * getAll
     *
     * @param $page int The first param of the limit
     * @param $offset int The number of items to retrieve
     * @param $order_field String Field to order
     * @param $order_direction String Direction
     * @param $filter String|null
     * @param $filter_params Array|null
     *
     * @package Core-o-Graphy
     */
    
    public function getAll (
        $page=1,
        $offset=7,
        $order_field='id',
        $order_direction='DESC',
        $filter='',
        $filter_params=array ()
    ) {
        
        // Prepare filter
        if ($filter) {
            $filter = ' WHERE ' . $filter;
        }

   
        // Prepare SQL statement     
        $sql = "
            SELECT
                " . $this->_table . ".*
            
            FROM
                " . $this->_table . "
            
            " . $filter . " 
            
            ORDER BY
                " . $order_field . ' ' . $order_direction . "
                
            LIMIT
                :limit, :offset
        ";
        
        
        // Prepare params
        $params = array (
            ':limit' => ($page - 1) * $offset,
            ':offset' => $offset
        );
        
        
        // Run connection
        $this->_connection->prepare ($sql, $params);
        $results = $this->_connection->execute ();
        
        
        // Bind data
        $result = array ();
        $class_name = $this->_class;
        foreach ($results as $row) {
            $result[] = new $class_name ($row);
        }
        
        return $result;
        
    }
    
    
    /**
     * getTotal
     *
     * Returns the total of results inside in a table
     *
     * @param $filter String|null
     * @param $filter_params Array|null
     *
     * @package UCC
     */
    
    public function getTotal ($filter='', $filter_params=array ()) {
        
        // Prepare filter
        $_new_filter = '';
        if ($filter) {
            $_new_filter = ' WHERE ' . $filter;
        }
        
   
        // Prepare SQL statement
        $sql = "
            SELECT
                COUNT(" . $this->_table . ".id)
            
            FROM
                " . $this->_table . "
                " . $_new_filter . " 
        ";
        
        
        $this->_connection->prepare ($sql);
        return $this->_connection->getTotal ();
        
    }
}