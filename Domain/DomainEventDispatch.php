<?php

namespace lwMembersearch\Domain;

class DomainEventDispatch 
{
    public function __construct()
    {
        
    }
    
    public static function getinstance()
    {
        return new DomainEventDispatch();
    }
    
    public function execute($event)
    {
        $DomainEventHandlerClass = "\\lwMembersearch\\Domain\\".$event->getDomainName()."\\EventHandler";
        $event->addEventHistory('Event passed to Domain Event Handler ['.__CLASS__.'->'.__FUNCTION__.': '.__LINE__.']');           
        $response = $DomainEventHandlerClass::getInstance()->execute($event);
        $event->addEventHistory('Event returned from Domain Event Handler ['.__CLASS__.'->'.__FUNCTION__.': '.__LINE__.']');           
        //echo "<pre>";print_r($event->getEventHistory());echo"</pre>";
        return $response;
    }
}