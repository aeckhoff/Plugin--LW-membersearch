<?php

namespace lwMembersearch\Domain\FB;

class EventHandler 
{
    public function __construct()
    {
        $this->dic = new \lwMembersearch\Services\dic();
    }
    
    public function getInstance()
    {
        return new EventHandler();
    }
    
    public function execute($event)
    {
        $this->event = $event;
        $method = $this->event->getEventName();
        return $this->$method();
    }    

    protected function getAllFbAggregate()
    {
        $aggregate = $this->dic->getFbRepository()->getAllObjectsByCategoryAggregate($this->event->getParameterByKey("categoryId"));
        $this->event->getResponse()->setDataByKey('allFbAggregate', $aggregate);
        return $this->event->getResponse();
    }    
    
    protected function getIsDeletableSpecification()
    {
        $this->event->getResponse()->setDataByKey('isDeletableSpecification', \lwMembersearch\Domain\FB\Specification\isValid::getInstance());
        return $this->event->getResponse();
    }
    
    protected function getFbEntityFromArray()
    {
        $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
        $entity = \lwMembersearch\Domain\FB\Model\Factory::getInstance()->buildNewObjectFromValueObject($dataValueObject);
        $this->event->getResponse()->setDataByKey('FbEntity', $entity);
        return $this->event->getResponse();        
    }

    public function add()
    {
        try {
            $dataValueObject = new \LWddd\ValueObject(array_merge(array("category_id"=>$this->event->getParameterByKey('categoryId')),$this->event->getDataByKey('postArray')));
            $result = $this->dic->getFbRepository()->saveObject(false, $dataValueObject);
            $this->event->getResponse()->setParameterByKey('saved', true);
        }
        catch (\LWddd\validationErrorsException $e) {
            $this->event->getResponse()->setDataByKey('error', $e->getErrors());
            $this->event->getResponse()->setParameterByKey('error', true);
        }
        return  $this->event->getResponse(); 
    }
    
    protected function getFbEntityById()
    {
        $entity = $this->dic->getFbRepository()->getObjectById($this->event->getParameterByKey('id'));
        $this->event->getResponse()->setDataByKey('FbEntity', $entity);
        return $this->event->getResponse();        
    }    
    
    public function save()
    {
        try {
            $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
            $result = $this->dic->getFbRepository()->saveObject($this->event->getParameterByKey('id'), $dataValueObject);
            $this->event->getResponse()->setParameterByKey('saved', true);
        }
        catch (\LWddd\validationErrorsException $e) {
            $this->event->getResponse()->setDataByKey('error', $e->getErrors());
            $this->event->getResponse()->setParameterByKey('error', true);
        }        
        return $this->event->getResponse();
    }    
    
    
    
    
    
    
    

  


    
    public function delete()
    {
        try {
            $ok = $this->dic->getFbRepository()->deleteObjectById($this->event->getParameterByKey('id'));
            $this->event->getResponse()->setParameterByKey('cmd', 'editGbForm');
            $this->event->getResponse()->setParameterByKey('id', $this->event->getParameterByKey('categoryId'));
            $this->event->getResponse()->setParameterByKey('response', 2);
            return $this->event->getResponse();
        }
        catch (\Exception $e) {
            $this->event->setParameterByKey('error', $e->getErrors());
            throw new \Exception();
        }         
    }
}