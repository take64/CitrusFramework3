<?php
/**
 * Smarty3.class.php.
 *
 *
 * PHP version 7
 *
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @package     Citrus
 * @subpackage  Library
 * @license     http://www.besidesplus.net/
 */

namespace Citrus\Library;

use Citrus\CitrusConfigure;

include_once dirname(__FILE__) . '/smarty3/Smarty.class.php';

class CitrusLibrarySmarty3 extends \Smarty
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplateDir(CitrusConfigure::$CONFIGURE_ITEM->paths->callTemplate());
        $this->setCompileDir(CitrusConfigure::$CONFIGURE_ITEM->paths->callCompile());
        $this->setCacheDir(CitrusConfigure::$CONFIGURE_ITEM->paths->callCache());
        $this->caching = 0;
        $this->addPluginsDir(dirname(__FILE__) . '/smarty3_plugins');
    }
}