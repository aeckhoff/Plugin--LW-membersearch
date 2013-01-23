<?php

namespace lwMembersearch\View;

class FbForm
{
    public function __construct($type)
    {
        $this->view = new \lw_view(dirname(__FILE__).'/templates/FbForm.tpl.phtml');
        $this->view->type = $type;
    }

    public function setCategoryId($id)
    {
        $this->categoryId = $id;
    }
    
    public function setEntity($entity)
    {
        $this->fb = $entity;
    }
    
    public function setErrors($errors=false)
    {
        $this->view->errors = $errors;
    }
    
    public function render()
    {
        if ($this->view->type == "add") {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"addFb", "category_id"=>$this->categoryId));
        }
        else {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"saveFb", "id" => $this->fb->getId(), "category_id"=>$this->categoryId));
        }
        $this->fb->renderView($this->view);
        $this->view->backUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"editGbForm", "id"=>$this->categoryId));
        return $this->view->render();
    }
}