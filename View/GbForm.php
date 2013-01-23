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
    
    public function render()
    {
        if ($this->view->type == "add") {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"addGb"));
        }
        else {
            $this->view->actionUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"saveGb", "id" => $this->gb->getId()));
            
            if (\lwMembersearch\Domain\GB\Specification\isDeletable::getInstance()->isSatisfiedBy($this->gb)) {
                $this->view->deleteAllowed = true;
                $this->view->deleteUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"deleteGb","id"=>$this->gb->getId()));
            }
        }
        $this->gb->renderView($this->view);
        $this->view->backUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"showGbList"));
        
        if ($this->view->type != "add") {
            $form = new \lwMembersearch\Domain\FB\View\FbList();
            $this->view->FbList = $form->render();
            $this->view->showFbList = true;
        }
        
        return $this->view->render();
    }
}