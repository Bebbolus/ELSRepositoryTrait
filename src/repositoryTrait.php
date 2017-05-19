<?php

namespace ELSRepositoryTrait;


use Bebbolus\edmelspackage\Models\User;

trait RepositoryTrait
{
    private $repo;

    function __construct()
    {

        if (method_exists($this, 'getPrivateKey')) $this->key  = $this->getPrivateKey();
        else {
            if(!isset($this->key)) $this->key  = 'code';
        }

//        if($this instanceof User)dd($this);

        if (method_exists($this, 'getReservedKey')) $this->reservedKey  = $this->getReservedKey();
        else {
            if(!isset($this->reservedKey)) $this->reservedKey  = [];
        }

        parent::__construct();
        $this->repo = new ELSBaseRepository($this, $this->getTypeName(), $this->getIndexName(), $this->key);
        foreach ($this->reservedKey as $key){
            $this->repo->addReservedKey($key);
        }
    }


    /*
     * PROPERTY RETRIEVE FUNCTION
     */

    public function getUniqueKey()
    {
        return $this->key;
    }

    public function getUniqueKeyValue()
    {
        return $this->{$this->key};
    }

    //MOdificato
    public function getCode()
    {
        return $this->code;
    }

    public function getElsId()
    {
        return $this->getAttributes()['id'];
    }

    /*
     * SEARCH FUNCTION
     */

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function quest($term, $page = 0)
    {
        return $this->repo->search($term, $page);
    }

    public function findByKey($keyValue)
    {
        return $this->repo->findByKey($keyValue);
    }

    public function get($conditions = [], $requiredField = [], $page = 0)
    {
        return $this->repo->get($conditions, $requiredField, $page);
    }

    public function getId($conditions = [], $requiredField = [])
    {
        return $this->repo->getId($conditions, $requiredField);
    }

    public function count($conditions = [], $requiredField = [])
    {
        return $this->repo->count($conditions, $requiredField);
    }

    /*
     * CRUD FUNCTION
     */

    public function indexWithId($content)
    {
        return $this->repo->indexWithId($this->getElsId(), $content);
    }

    public function index($content)
    {
        return $this->repo->index($content);
    }

    public function update(array $attributes = [], array $options = [])
    {

        foreach ($attributes as $k=>$v){
            $this->attributes[$k] = $v;
        }

        $this->attributes['updated_at']  =time();

        if(!isset($this->attributes['created_at'])) $this->attributes['created_at'] = time();
        else{
            if(!is_int($this->attributes['created_at'])) $this->attributes['created_at'] = strtotime($this->attributes['created_at']);
        }

        $this->repo->forceDestroy($this->getElsId());
        try{
            return $this->repo->indexWithId($this->getElsId(), $this->getAttributes());
        }catch ( EntityNotCreatedException $e){
            LOG::error('[ERROR] CANNOT RE-INDEX WITH ID THE FOLLOWING DOCS: '.PHP_EOL.json_encode($this->getAttributes()));
            throw new EntityNotUpdatedException();
        }
    }


    public function save(array $options = [])
    {
        return $this->repo->save();
    }

    public function delete()
    {
        return $this->repo->delete($this->getElsId());
    }

    public function forceDestroy()
    {
        return $this->repo->forceDestroy($this->getElsId());
    }

    public function indexExist()
    {
        return $this->repo->indexExist();
    }

    public function verifyUniqueKey($value)
    {
        return $this->repo->verifyUniqueKey($value);
    }

}
