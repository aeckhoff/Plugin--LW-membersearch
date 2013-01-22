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
    
    protected function returnRenderedView($view)
    {
        $this->event->getResponse()->setOutputByKey('output', $view->render());
        return $this->event->getResponse();
    }    
    
    public function getAddFormView()
    {
        $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
        $this->event->addEventHistory('built ValueObject from postArray ['.__CLASS__.'->'.__FUNCTION__.': '.__LINE__.']');
        $entity = \lwMembersearch\Domain\FB\Model\Factory::getInstance()->buildNewObjectFromValueObject($dataValueObject);
        $this->event->addEventHistory('built FB entity from ValueObject ['.__CLASS__.'->'.__FUNCTION__.': '.__LINE__.']');
        $formView = new \lwMembersearch\Domain\FB\View\Form('add', $entity);
        $this->event->addEventHistory('passed to form View ['.__CLASS__.'->'.__FUNCTION__.': '.__LINE__.']');
        $formView->setEvent($this->event);
        $formView->setErrors($this->event->getParameterByKey('error'));
        $this->event->addEventHistory('built FB Add Form ['.__CLASS__.'->'.__FUNCTION__.': '.__LINE__.']');
        return $this->returnRenderedView($formView);
    }
    
    public function add()
    {
        try {
            $dataValueObject = new \LWddd\ValueObject(array_merge(array("category_id"=>$this->event->getParameterByKey('categoryId')),$this->event->getDataByKey('postArray')));
            $result = $this->dic->getFbRepository()->saveObject(false, $dataValueObject);
            $this->event->getResponse()->setParameterByKey('cmd', 'editGbForm');
            $this->event->getResponse()->setParameterByKey('response', 1);
            $this->event->getResponse()->setParameterByKey('id', $this->event->getParameterByKey('categoryId'));
            return $this->event->getResponse();
        }
        catch (\LWddd\validationErrorsException $e) {
            $this->event->setParameterByKey('error', $e->getErrors());
            return $this->getAddFormView($PostArray, $e->getErrors());
        }        
    }

    public function getEditFormView()
    {
        if ($this->event->getParameterByKey('error')) {
            $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
            $entity = \lwMembersearch\Domain\FB\Model\Factory::getInstance()->buildNewObjectFromValueObject($dataValueObject);
            $entity->setId($this->event->getParameterByKey('id'));
        }
        else {
            $entity = $this->dic->getFbRepository()->getObjectById($this->event->getParameterByKey('id'));
        }
        $formView = new \lwMembersearch\Domain\FB\View\Form('edit', $entity);
        $formView->setEvent($this->event);
        $formView->setErrors($this->event->getParameterByKey('error'));
        return $this->returnRenderedView($formView);        
    }    

    public function save()
    {
        try {
            $dataValueObject = new \LWddd\ValueObject($this->event->getDataByKey('postArray'));
            $result = $this->dic->getFbRepository()->saveObject($this->event->getParameterByKey('id'), $dataValueObject);
            $this->event->getResponse()->setParameterByKey('cmd', 'editGbForm');
            $this->event->getResponse()->setParameterByKey('response', 1);
            $this->event->getResponse()->setParameterByKey('id', $this->event->getParameterByKey('categoryId'));
            return $this->event->getResponse();
        }
        catch (\LWddd\validationErrorsException $e) {
            $this->event->setParameterByKey('error', $e->getErrors());
            return $this->getEditFormView();
        }
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