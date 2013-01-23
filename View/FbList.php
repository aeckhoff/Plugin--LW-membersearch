<?php

namespace lwMembersearch\View;

class FbList
{
    public function __construct()
    {
        $this->dic = new \lwMembersearch\Services\dic();
        $this->view = new \lw_view(dirname(__FILE__).'/templates/FbList.tpl.phtml');
    }
    
    public function setAggregate($aggregate)
    {
        $this->view->fbs = $aggregate;
    }
    
    public function setIsDeletableSpecification($isDeletable)
    {
        $this->view->deletableSpecification = $isDeletable;
    }    
    
    public function render()
    {
        $this->view->newUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"addFbForm", "category_id"=>$this->dic->getLWRequest()->getInt("id")));
        $this->view->categoryId = $this->dic->getLWRequest()->getInt("id");
        return $this->view->render();
    }
}