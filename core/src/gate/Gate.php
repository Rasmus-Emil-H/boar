<?php

/**
|----------------------------------------------------------------------------
| Application authorization
|----------------------------------------------------------------------------
| From here you can control access between entities and actions
| 
| @author RE_WEB
| @package \app\core\src\gate
|
*/

class Gate {

    public function __construct(
        protected object $policies
    ) {
        
    }

    protected function allows(object $action): bool {
        if (!isset($this->policies->{$action})) return false;
        $actionName = $action->get('name');
        return $this->policies->{$actionName}(); 
    }

}