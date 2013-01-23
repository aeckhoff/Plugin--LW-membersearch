<?php

namespace lwMembersearch\Domain\GB;

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

    protected function getAllGbAggregate()
    {
        $aggregate = $this->dic->getGbRepository()->getAllObjectsAggregate();
        $this->event->getResponse()->setDataByKey('allGbAggregate', $aggregate);
        return $this->event->getResponse();
    }
    
    protected function getIsDeletableSpecification()
    {
        $this->event->getResponse()->setDataByKey('isDeletableSpecification', \lwMembersearch\Domain\GB\Specification\isValid::getInstance());
        return $this->event->getResponse();
    }
    
    protected function getGbEntityFromArray()
    {
        $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
        $entity = \lwMembersearch\Domain\GB\Model\Factory::getInstance()->buildNewObjectFromValueObject($dataValueObject);
        $this->event->getResponse()->setDataByKey('GbEntity', $entity);
        return $this->event->getResponse();
    }

    public function add()
    {
        try {
            $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
            $result = $this->dic->getGbRepository()->saveObject(false, $dataValueObject);
            $this->event->getResponse()->setParameterByKey('saved', true);
        }
        catch (\LWddd\validationErrorsException $e) {
            $this->event->getResponse()->setDataByKey('error', $e->getErrors());
            $this->event->getResponse()->setParameterByKey('error', true);
        }
        return  $this->event->getResponse();
    }     
    
    protected function getGbEntityById()
    {
        $entity = $this->dic->getGbRepository()->getObjectById($this->event->getParameterByKey('id'));
        $this->event->getResponse()->setDataByKey('GbEntity', $entity);
        return $this->event->getResponse();        
    }
    
    public function save()
    {
        try {
            $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
            $result = $this->dic->getGbRepository()->saveObject($this->event->getParameterByKey('id'), $dataValueObject);
            $this->event->getResponse()->setParameterByKey('saved', true);
        }
        catch (\LWddd\validationErrorsException $e) {
            $this->event->getResponse()->setDataByKey('error', $e->getErrors());
            $this->event->getResponse()->setParameterByKey('error', true);
        }        
        return $this->event->getResponse();
    }
    
    protected function deleteById()
    {
        try {
            $this->dic->getGbRepository()->deleteObjectById($this->event->getParameterByKey('id'));
            $this->event->getResponse()->setParameterByKey('cmd', 'showGbList');
            $this->event->getResponse()->setParameterByKey('response', 2);
            return $this->event->getResponse();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }        
    }    
}