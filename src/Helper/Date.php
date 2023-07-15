<?php

namespace Nexche\Helper;

/**
 * Date time format helper class for PHP
 */
class Date {

    /**
     * Static instance variable.
     * 
     * @var object
     */
    public static $instance = null ;
    /**
     * Internal variable for storing set of date format profiles
     * 
     * - db
     *  - date
     *  - time
     *  - datetime
     * - user
     *  - date
     *  - time
     *  - datetime
     * 
     * @var arrray
     */
    private $profiles = array();
    /**
     * Static copy of the profiles
     * 
     * @var array
     */
    private static $staticProfiles = array();

    /**
     * The active profile name
     * 
     * @var string
     */
    private $active = null;
    /**
     * Static variable to hold the active profile name 
     * 
     * @var string
     */
    private static $staticActive = null;
    /**
     *  date format profile array key
     * 
     * @var string
     */
    private static $KEY_DATE = 'date';

    /**
     * time format profile array key
     * 
     * @var string
     */
    private static $KEY_TIME = 'time';

    /**
     * date & time format profile array key
     * @var string
     */
    private static $KEY_DATETIME = 'datetime';
    /**
     * Allowed formtting methods.
     * @var type
     */
    private static $methods = [
        'date' => 'date',
        'now' => 'now',
        'time' => 'time',
        'datetime' => 'datetime'
    ];
    /**
     * 
     * @param array $profiles
     * 
     * @param string $lockName profile name
     */
    public function __construct($profiles = null, $lockName = null ) {
        if( self::$instance == null ) {
            self::$instance = $this ;
        }
        if( $profiles !== null ){
            $this->setProfiles($profiles, $lockName ) ;
        }
    }
    /**
     * Set date format profiles. This method will replace any existing profiles.
     * 
     * @param string $name
     * @param array $profiles
     */
    public function setProfiles($profiles, $lock = null) {
        $this->profiles = $profiles;
        $this->active = null ;

        if ($lock) {
            $this->lock($lock);
        }
        return $this;
    }

    /**
     * Add a date format profile
     * 
     * @param string $name
     * @param array $profiles
     */
    public function addProfile($name, $profile) {
        $this->profiles[$name] = $profile;

        return $this;
    }

    /**
     * Remove a profile by its name
     * @param string $name
     */
    public function removeProfile($name) {
        if (isset($this->profiles[$name])) {
            unset($this->profiles[$name]);
        }

        return $this;
    }

    /**
     * Get profiles config by its name.
     * 
     * @param type $key
     */
    public function getProfile($key) {
        if (isset($this->profiles[$key])) {
            return $this->profiles[$key];
        }
        return null;
    }

    /**
     * Get a key value from profile specified.
     * 
     * @param type $key
     */
    public function getProfileValue($profileName, $key) {
        if (isset($this->profiles[$profileName][$key])) {
            return $this->profiles[$profileName][$key];
        }
        return null;
    }

    /**
     * Get locked profiles name.
     * 
     * @return type
     */
    public function getLockedProfileName() {
        return $this->active;
    }

    /**
     * Get locked profiles name.
     * 
     * @return type
     */
    public function getLockedProfile() {
        if (isset($this->profiles[$this->active])) {
            return $this->profiles[$this->active];
        }
        return null;
    }

    /**
     * php current time in user format
     * 
     * @return string
     */
    public function now($profilesName) {

        return $this->_format(time(), $profilesName, self::$KEY_DATETIME);
    }

    /**
     * Convert given string date (date or date-time) to user date format
     * @param string|int $dt
     * 
     * @return string
     */
    public function date($dt, $profilesName = null) {
        
        return $this->_format($dt, $profilesName, self::$KEY_DATE);
    }

    /**
     * Convert given string date (time or date-time) to user time format
     * @param string|int $dt
     * @return string
     */
    public function time($dt, $profilesName = null) {
        return $this->_format($dt, $profilesName, self::$KEY_TIME);
    }

    /**
     * Convert given string date-time to user date-time format
     * @param string|int $dt
     * @return string
     */
    public function datetime($dt, $profilesName = null) {
        return $this->_format($dt, $profilesName, self::$KEY_DATETIME);
    }

    /**
     * Private date format function
     * 
     * @param type $dt
     * @param type $profilesName
     * @param type $key
     * @return type
     */
    private function _format($dt, $profilesName, $key) {
        if ($dt === null) {
            return null;
        }
        if ($profilesName === null) {
            $profilesName = $this->getLockedProfileName();
        }
        if (is_numeric($dt)) {
            $t = $dt;
        } else {
            $t = strtotime($dt);
        }

        if ($t && $dt != '0000-00-00' && $dt != '0000-00-00 00:00:00' && $t != -19800) {
            $profiles = $this->getProfile($profilesName);
            if( ! isset($profiles[$key]) ) {
                throw new \Exception('The profile \'' . $key . '\' does not exists.');
            }
            else {
                $format = $profiles[$key];
                return Date($format, $t);
            }
            
        }

        return null;
    }

    /**
     * make the current profiles  and active profiles settings static-persistent
     * @return $this
     */
    public function lock($name) {
        $this->active = $name;
        return $this;
    }

    /**
     * Statically set profiles.
     * 
     * @return $this
     */
    public function persist() {
        Date::$staticProfiles = $this->profiles;
        Date::$staticActive = $this->active;
        return $this;
    }


    /**
     * Dynamic method resolver.
     * 
     * @param string $method
     * @param array $parameters
     * @return type
     */
    public function __call(string $method, array $parameters) {
        return self::invokeMethod($this, $method, $parameters) ;
    }
    /**
     * The static method handler
     * 
     * @param string $method
     * @param array $parameters
     * @return type
     */
    public static function __callStatic(string $method, array $parameters) {
        $obj = self::$instance->setProfiles(Date::$staticProfiles, Date::$staticActive);

        return self::invokeMethod($obj, $method, $parameters) ;
    }
    /**
     * The actual formatting method.
     * 
     * @param object $obj 
     * @param string $method
     * @param array $parameters
     * @return string
     * @throws \Exception
     */
    private static function invokeMethod($obj, $method, $parameters) {
        
        $profiles = preg_replace('/(datetime|date|time|now)$/', "", strtolower($method) ) ;

        $inMethod = strtolower( str_ireplace($profiles, "", $method) );

        
        if( count($parameters) == 0 && $inMethod !== 'now' ) {
            $parameters[] = time() ;
        }
        $parameters[] = $profiles ;
        
        if (!array_key_exists($inMethod, self::$methods)) {
            throw new \Exception('The ' . $inMethod . ' is not supported.');
        }
        

        return call_user_func_array(array($obj, self::$methods[$inMethod]), $parameters);
    }

}