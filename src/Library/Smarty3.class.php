<?php
/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Library;

use Citrus\Configure;

include_once dirname(__FILE__) . '/smarty3/Smarty.class.php';

class Smarty3 extends \Smarty
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplateDir(Configure::$CONFIGURE_ITEM->paths->callTemplate());
        $this->setCompileDir(Configure::$CONFIGURE_ITEM->paths->callCompile());
        $this->setCacheDir(Configure::$CONFIGURE_ITEM->paths->callCache());
        $this->caching = 0;
        $this->addPluginsDir([dirname(__FILE__) . '/smarty3_plugins']);
    }
}