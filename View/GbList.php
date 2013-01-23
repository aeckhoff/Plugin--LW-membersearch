<?php

namespace lwMembersearch\View;

class GbList
{
    public function __construct()
    {
        $this->view = new \lw_view(dirname(__FILE__).'/templates/GbList.tpl.phtml');
    }
    
    public function setAggregate($aggregate)
    {
        $this->view->gbs = $aggregate;
    }
    
    public function setIsDeletableSpecification($isDeletable)
    {
        $this->view->deletableSpecification = $isDeletable;
    }
    
    public function render()
    {
        $this->view->backUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"buildBackendMenu"));
        $this->view->newUrl = \lw_page::getInstance()->getUrl(array("cmd"=>"addGbForm"));
        return $this->view->render();
    }
}