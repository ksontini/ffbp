<?php
namespace Models;

use DB\SQL\Mapper;

class ModelBase extends Mapper
{

    /**
     * @var string
     * The database table name
     */
    protected $tableName;

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
        return parent::__construct($db, $this->tableName);
    }

    /**
     * Find and retrieve the object. If it doesn't exist return false.
     * @param $id
     * @return array|FALSE
     */
    public function findById($id)
    {
        return $this->load(array("id_$this->tableName=?", $id));
    }

    /**
     * Find by criteria
     * @param $criteria
     * @return array|FALSE
     */
    public function findBy($criteria = array())
    {
        return $this->load($criteria);

    }


    /**
     * * Find All by criteria paginated
     * @param int $page
     * @param array $criteria
     * @param array $options
     * @param int $max
     * @return \array[]|FALSE
     */
    public function findAll($page = 0, $criteria = array(), $options = array(),$max=10)
    {
        return $this->paginate($page, $max, $criteria, $options);
    }

    /**
     * Find All by criteria
     * @param array $criteria
     * @param array $options
     * @return static[]
     */
    public function find($criteria = array(), Array $options = NULL, $ttl=0)
    {
        return parent::find($criteria, $options,$ttl=0);
    }

    /**
     * Get Map to first record in cursor
     */
    public function goFirst ($pagination)
    {
        return $pagination->first();
    }

    /**
     * Get Map to last record in cursor
     */
    public function goLast()
    {
       $this->load();
        return $this->last();
    }
}