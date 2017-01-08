<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ($ENV['website_host'] === $ENV['host'])
     $RTR->_controller_rebase('website');
else if (!count($URI->segments))
     $RTR->set_class('browse');
// all pages that are allowed on the newsroom
// either use their own controller
// or have rewrite rules to the browse controller
// ===> all others handled by website
else $RTR->_controller_rebase('website');

throw new Controller_Pass_Exception();

?>