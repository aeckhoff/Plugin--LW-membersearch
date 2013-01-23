<?php

namespace lwMembersearch\View;

class GbForm
{
    public function __construct($type)
    {
        $this->view = new \lw_view(dirname(__FILE__).'/templates/GbForm.tpl.phtml');
        $this->view->type = $type;
    }
    
    public function setEntity($entity)
    {
        $this->gb = $entity;
    }
    
    public function setErrors($errors=false)
    {
        $this->view->errors = $errors;
    }
    
    public function setFbListView($view)
    {
         $this->view->FbList = $view;
         $this->view->showFbList = true;
    }
    
    public function render()
    {
        if ($this->view->type == "add") {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"addGb"));
        }
        else {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"saveGb", "id" => $this->gb->getId()));
        }
        $this->gb->renderView($this->view);
        $this->view->backUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"showGbList"));
        
        return $this->view->render();
    }
}