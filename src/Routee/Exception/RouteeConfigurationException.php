<?php
/**
  *
  * Cofiguration related exception is to handle here.
  *
  * @package Routee\Exception
  * @author kesavamoorthi<kesav@stallioni.com>,nandhakumar<nandha@stallioni.com>
  *
  * @return void
  */
namespace Routee\Exception;

/**
 * Class RouteeConfigurationException
 *
 * @package Routee\Exception
 */
class RouteeConfigurationException extends \Exception
{

    /**
     * Default Constructor
     *
     * @param string|null $message
     * @param int  $code
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
