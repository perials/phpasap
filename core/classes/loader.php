<?php
namespace core\classes;

trait Loader {
    // these are the properties to be lazy loaded from app
    private $app_properties = ['view', 'request', 'validator', 'session', 'db', 'form'];
    
    public function __get($property) {
        if (in_array($property, $this->app_properties)) {
            $this->{$property} = &$this->app->{$property};
            return $this->{$property};
        }
        return null;
    }
}
