<?php

/**************************************************************************
*  Copyright notice
*
*  Copyright 2013 Logic Works GmbH
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*  http://www.apache.org/licenses/LICENSE-2.0
*  
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License.
*  
***************************************************************************/

namespace lwMembersearch\Controller;

class BackendController
{
    public function __construct($response)
    {
        $this->defaultAction = "showListAction";
        $this->dic = new \lwMembersearch\Services\dic();
        $this->request = $this->dic->getLwRequest();
        $this->dispatch = \lwMembersearch\Domain\DomainEventDispatch::getInstance();
    }
    
    public function execute()
    {
        $methodName = $this->request->getAlnum('cmd')."Action";
        if (method_exists($this, $methodName)) {
            return call_user_method($methodName, $this);
        }
        else {
            return $this->buildBackendMenuAction();
        }
    }
    
    protected function returnRenderedView($view)
    {
        $response = \LWddd\Response::getInstance();
        $response->setOutputByKey('output', $view->render());
        return $response;
    }
    
    protected function showGbListAction()
    {
        $view = new \lwMembersearch\View\GbList();

        $event = \LWddd\DomainEvent::getInstance('GB', 'getAllGbAggregate');
        $view->setAggregate($this->dispatch->execute($event)->getDataByKey('allGbAggregate'));

        $event = \LWddd\DomainEvent::getInstance('GB', 'getIsDeletableSepcification');
        $view->setIsDeletableSpecification($this->dispatch->execute($event)->getDataByKey('isDeletableSepcification'));
        
        return $this->returnRenderedView($view);
    }    
    
    protected function addGbFormAction($error = false)
    {
        $formView = new \lwMembersearch\View\GbForm('add');

        $event = \LWddd\DomainEvent::getInstance('GB', 'getGbEntityFromArray')
                ->setDataByKey('postArray', $this->request->getPostArray());
        $formView->setEntity($this->dispatch->execute($event)->getDataByKey('GbEntity'));
        $formView->setErrors($error);
        return $this->returnRenderedView($formView);
    }    
    
    protected function addGbAction()
    {
        $event = \LWddd\DomainEvent::getInstance('GB', 'add')
                ->setDataByKey('postArray', $this->request->getPostArray());
        $response = $this->dispatch->execute($event);
        if ($response->getParameterByKey("error")) {
            return $this->addGbFormAction($response->getDataByKey("error"));
        }
        $response = \LWddd\Response::getInstance();
        $response->setParameterByKey('cmd', 'showGbList');
        $response->setParameterByKey('response', 1);
        return $response;
    }     
    
    protected function editGbFormAction($error=false)
    {
        if ($error) {
            $event = \LWddd\DomainEvent::getInstance('GB', 'getGbEntityFromArray')
                    ->setDataByKey('postArray', $this->request->getPostArray());
            $entity = $this->dispatch->execute($event)->getDataByKey('GbEntity');
            $entity->setId($this->request->getInt("id"));
        }
        else {
            $event = \LWddd\DomainEvent::getInstance('GB', 'getGbEntityById')
                    ->setParameterByKey("id", $this->request->getInt("id"));
            $entity = $this->dispatch->execute($event)->getDataByKey('GbEntity');
        }
        $formView = new \lwMembersearch\View\GbForm('edit');
        $formView->setEntity($entity);
        $formView->setErrors($error);
        return $this->returnRenderedView($formView);
    }    
    
    protected function saveGbAction()
    {
        $event = \LWddd\DomainEvent::getInstance('GB', 'save')
                ->setParameterByKey("id", $this->request->getInt("id"))
                ->setDataByKey('postArray', $this->request->getPostArray());
        $response = $this->dispatch->execute($event);
        if ($response->getParameterByKey("error")) {
            return $this->editGbFormAction($response->getDataByKey("error"));
        }
        $response = \LWddd\Response::getInstance();
        $response->setParameterByKey('cmd', 'showGbList');
        $response->setParameterByKey('response', 1);
        return $response;        
    } 
    
    protected function deleteGbAction()
    {
        $event = \LWddd\DomainEvent::getInstance('GB', 'deleteById')
                ->setParameterByKey("id", $this->request->getInt("id"));
        return $this->dispatch->execute($event);
    }    
    
    protected function addFbFormAction($errors=false)
    {
        $event = \LWddd\DomainEvent::getInstance('FB', 'getAddFormView')
                ->setDataByKey('postArray', $this->request->getPostArray())
                ->addEventHistory('Event created ['.__CLASS__.'->'.__FUNCTION__.': '.__LINE__.']');
        return $this->dispatch->execute($event);
    }
    
    protected function addFbAction()
    {
        $event = \LWddd\DomainEvent::getInstance('FB', 'add')
                ->setDataByKey('postArray', $this->request->getPostArray())
                ->setParameterByKey('categoryId', $this->request->getInt("category_id"));
        return $this->dispatch->execute($event);
    }
    
    protected function editFbFormAction($errors=false)
    {
        $event = \LWddd\DomainEvent::getInstance('FB', 'getEditFormView')
                ->setDataByKey('postArray', $this->request->getPostArray())
                ->setParameterByKey('id', $this->request->getInt("id"))
                ->setParameterByKey('categoryId', $this->request->getInt("category_id"));
        return $this->dispatch->execute($event);
    }    
    
    protected function saveFbAction()
    {
        $event = \LWddd\DomainEvent::getInstance('FB', 'save')
                ->setDataByKey('postArray', $this->request->getPostArray())
                ->setParameterByKey('id', $this->request->getInt("id"))
                ->setParameterByKey('categoryId', $this->request->getInt("category_id"));
        return $this->dispatch->execute($event);
    }
    
    protected function deleteFbAction()
    {
        $event = \LWddd\DomainEvent::getInstance('FB', 'delete')
                ->setParameterByKey('id', $this->request->getInt("id"))
                ->setParameterByKey('categoryId', $this->request->getInt("category_id"));
        return $this->dispatch->execute($event);
    }
    
    protected function buildBackendMenuAction()
    {
        $response = \LWddd\Response::getInstance();
        $view = new \lwMembersearch\View\backendMenu();
        $response->setOutputByKey('output', $view->render());
        return $response;
    }    
}