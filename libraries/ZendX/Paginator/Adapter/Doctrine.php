<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Doctrine.php,v 1.1 2010/12/02 16:16:25 bart Exp $
 */

/**
 * @see Zend_Paginator_Adapter_Interface
 */
require_once 'Zend/Paginator/Adapter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_Paginator_Adapter_Doctrine implements Zend_Paginator_Adapter_Interface
{
    /**
     * Name of the row count column
     *
     * @var string
     */
    const ROW_COUNT_COLUMN = 'zend_paginator_row_count';

    /**
     * Database query
     *
     * @var Doctrine_Query
     */
    protected $_query = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $_rowCount = null;

    /**
     * Constructor.
     *
     * @param Doctrine_Query $query The select query
     */
    public function __construct(Doctrine_Query $query)
    {
        $this->_query = $query;
    }

    /**
     * Sets the total row count, either directly or through a supplied query
     *
     * @param  Doctrine_Query|integer $totalRowCount Total row count integer 
     *                                               or query
     * @return Zend_Paginator_Adapter_Doctrine $this
     * @throws Zend_Paginator_Exception
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof Doctrine_Query) {
            $sql = $rowCount->getSql();
            
            if (false === strpos($sql, self::ROW_COUNT_COLUMN)) {
                /**
                 * @see Zend_Paginator_Exception
                 */
                require_once 'Zend/Paginator/Exception.php';
                throw new Zend_Paginator_Exception('Row count column not found');
            }
            
            $result = $rowCount->fetchOne()->toArray();
            
            $this->_rowCount = count($result) > 0 ? $result[self::ROW_COUNT_COLUMN] : 0;
        } else if (is_integer($rowCount)) {
            $this->_rowCount = $rowCount;
        } else {
            /**
             * @see Zend_Paginator_Exception
             */
            require_once 'Zend/Paginator/Exception.php';
            throw new Zend_Paginator_Exception('Invalid row count');
        }

        return $this;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_query->limit($itemCountPerPage)->offset($offset);
        
        return $this->_query->execute();
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->_rowCount === null) {
            $rowCount = $this->_query->count();
            $this->setRowCount($rowCount);
        }

        return $this->_rowCount;
    }
}
