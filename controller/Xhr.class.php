<?php
/**
 * @copyright   Copyright 2017, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Controller;


use Citrus\CitrusConfigure;
use Citrus\CitrusException;
use Citrus\CitrusFormmap;
use Citrus\CitrusLogger;
use Citrus\CitrusMessage;
use Citrus\CitrusService;
use Citrus\CitrusSession;
use Citrus\Database\CitrusDatabaseColumn;
use Citrus\Document\CitrusDocumentPager;
use Citrus\Message\CitrusMessageItem;
use Citrus\Sqlmap\CitrusSqlmapCondition;
use Citrus\Sqlmap\CitrusSqlmapException;
use Citrus\Xhr\CitrusXhrElement;
use Citrus\Xhr\CitrusXhrResult;

class CitrusControllerXhr
{
    /** @var CitrusFormmap citrus formmap object */
    public $formmap = null;

    /** @var string formmap id */
    protected $formmap_namespace = '';

    /** @var string formmap edit id */
    protected $formmap_edit_id = '';

    /** @var string formmap view id */
    protected $formmap_view_id = '';

    /** @var string formmap call id */
    protected $formmap_call_id = '';

    /** @var string formmap toggle id */
    protected $formmap_toggle_id = '';

    /** @var string formmap suggest id */
    protected $formmap_suggest_id = '';

    /** @var array serach to like */
    protected $search_column_to_like = [];

    /** @var string default orderby */
    protected $default_orderby = '';

    /** @var string suggest orderby */
    protected $suggest_orderby = '';

    /** @var CitrusService service  */
    protected $service = null;

    /** @var array remove column summaries */
    protected $remove_column_summaries = [
        'schema', 'modified_at', 'condition',
        ];

    /** @var array remove column summaries is empty */
    protected $remove_column_summaries_is_empty = [
        'count', 'sum', 'avg', 'max', 'min', 'name', 'id',
    ];

    /** @var array remove column view */
    protected $remove_column_view = [
        'schema', 'modified_at', 'condition',
    ];

    /** @var array remove column view is empty */
    protected $remove_column_view_is_empty = [
        'count', 'sum', 'avg', 'max', 'min', 'name', 'id',
    ];



    /**
     * controller run
     */
    public function run()
    {
        // jquery jsonp callback
        $callback_code = null;

        $response = null;

        try
        {
            $actionName = CitrusSession::$router->action;
            
            $result = new CitrusXhrElement();
            $this->initialize();
            $result->results = $this->$actionName();
            $this->release();
            $result->messages = CitrusMessage::callItems();
            $response = $result;
        }
        catch (CitrusException $e)
        {
            CitrusMessage::addError($e->getMessage());
            $result = new CitrusXhrElement();
            $message = '実行時エラーが検出されました。';
            $result->exceptions = new CitrusException($message);
            $result->messages = [ new CitrusMessageItem($message, CitrusMessageItem::TYPE_ERROR) ];
            $result->results = new CitrusXhrResult();
            CitrusLogger::error($result);
            $response = $result;
            CitrusMessage::removeAll();
        }

        $response_json = json_encode($response);
        if (empty($callback_code) === true)
        {
            echo $response_json;
        }
        else
        {
            echo $callback_code . '(' . $response_json . ')';
        }
    }



    /**
     * initialize method
     *
     * @return string|null
     */
    protected function initialize()
    {
        return null;
    }



    /**
     * release method
     *
     * @return string|null
     */
    protected function release()
    {
        return null;
    }



    /**
     * call formmap element
     *
     * @return CitrusFormmap
     */
    protected function callFormmap() : CitrusFormmap
    {
        if (is_null($this->formmap) === true)
        {
            CitrusFormmap::initialize(CitrusConfigure::$CONFIGURE_PLAIN_DEFAULT, CitrusConfigure::$CONFIGURE_PLAIN_DOMAIN);
            $this->formmap = new CitrusFormmap();
        }
        return $this->formmap;
    }



    /**
     * call faces summary list
     * サマリリストの取得
     *
     * @return CitrusXhrResult
     */
    public function facesSummaries() : CitrusXhrResult
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace . '.php');
        $this->callFormmap()->bind();
        /** @var CitrusDatabaseColumn|CitrusSqlmapCondition $condition */
        $condition = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_call_id);
        $condition->toLike($this->search_column_to_like);

        // validate
        if ($this->callFormmap()->validate($this->formmap_call_id) > 0)
        {
            $result = new CitrusXhrResult();
        }
        else
        {
            // condition
            if (empty($condition->orderby) === true)
            {
                $condition->orderby = $this->default_orderby;
            }
            $condition->pageLimit();

            // call list
            $list = $this->callService()->facesSummaries($condition);
            $count = 0;

            // data exist
            if (empty($list) === false)
            {
                // call count
                $count = $this->callService()->count($condition);
                // modify
                foreach ($list as &$one)
                {
                    $one->remove($this->remove_column_summaries);
                    $one->removeIsEmpty($this->remove_column_summaries_is_empty);
                    $one->null2blank();
                }
            }

            $result = new CitrusXhrResult($list);
            $result->pager = new CitrusDocumentPager($condition->page, $count, $condition->limit, 7);
        }

        return $result;
    }



    /**
     * call summary record
     * サマリの取得
     *
     * @return  CitrusXhrResult
     * @throws CitrusSqlmapException
     */
    public function facesDetail() : CitrusXhrResult
    {
        // condition
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();
        /** @var CitrusDatabaseColumn|CitrusSqlmapCondition $condition */
        $condition = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_view_id);

        // call detail
        $detail = $this->callService()->facesDetail($condition);

        // modify
        $detail->remove($this->remove_column_view);
        $detail->removeIsEmpty($this->remove_column_view_is_empty);
        $detail->null2blank();

        return new CitrusXhrResult($detail->properties());
    }

    /**
     * regist item
     * の登録
     *
     * @return  CitrusXhrResult
     */
    public function modify()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();

        // validate
        if ($this->callFormmap()->validate($this->formmap_edit_id) > 0)
        {
            $result = false;
        }
        else
        {
            /** @var CitrusDatabaseColumn $entity */
            $entity = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_edit_id);
            if (empty($entity->callCondition()->rowid) === false && empty($entity->callCondition()->rev) === false)
            {
                $result = $this->callService()->modify($entity);
            }
            else
            {
                $result = $this->callService()->regist($entity);
            }
        }

        return new CitrusXhrResult([$result]);
    }

    /**
     * remove & item
     * の削除
     *
     * @return  CitrusXhrResult
     * @throws CitrusSqlmapException
     */
    public function remove()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();

        // remove
        /** @var CitrusDatabaseColumn $entity */
        $entity = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_edit_id);
        return new CitrusXhrResult([$this->callService()->remove($entity->getCondition())]);
    }
    
    /**
     * call summary list
     * サマリリストの取得
     *
     * @return  CitrusXhrResult
     * @throws CitrusSqlmapException
     */
    public function selections()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();

        // condition
        /** @var CitrusDatabaseColumn|CitrusSqlmapCondition $condition */
        $condition = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_call_id);
        $condition->toLike($this->search_column_to_like);

        // condition
        if (empty($condition->orderby) === true)
        {
            $condition->orderby = $this->default_orderby;
        }
        $condition->pageLimit();
        
        // count
        
        // call list
        $list = $this->callService()->selections($condition);
        $count = 0;

        // data exist
        if (empty($list) === false)
        {
            // call count
            $count = $this->callService()->count($condition);
            // modify
            foreach ($list as &$one)
            {
                $one->remove(array('status', 'schema',
                                   'resist_user_cd', 'resist_timestamp',
                                   'modify_user_cd', 'modify_timestamp',
                                   'condition'));
                $one->null2blank();
            }
        }

        $result = new CitrusXhrResult($list);
        $result->pager = new CitrusDocumentPager($condition->page, $count, $condition->limit, 7);

        return $result;
    }



    /**
     * call summary list
     * サマリリストの取得
     *
     * @return  CitrusXhrResult
     * @throws CitrusSqlmapException
     */
    public function suggests()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();
        
        // call
        /** @var CitrusDatabaseColumn|CitrusSqlmapCondition $condition */
        $condition = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_suggest_id);
        if (empty($condition->orderby) === true)
        {
            $condition->orderby = $this->suggest_orderby;
        }
        $condition->pageLimit(1,$condition->limit);
        $condition->toLike($this->search_column_to_like);
        $list = $this->callService()->names($condition);

        $result = [];
        foreach ($list as $one)
        {
            $result[] = array('label' => $one->name, 'value' => $one->id);
        }

        return new CitrusXhrResult($result);
    }

    /**
     * on document_role toggle
     * 画面ロールの登録
     *
     * @access  public
     * @return  CitrusXhrResult
     */
    public function on()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();

        // validate
        if ($this->callFormmap()->validate($this->formmap_toggle_id) > 0)
        {
            $result = false;
        }
        else
        {
            /** @var CitrusDatabaseColumn $entity */
            $entity = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_toggle_id);
            if (empty($entity->getCondition()->rowid) === false && empty($entity->getCondition()->rev) === false)
            {
                $result = $this->callService()->modify($entity);
            }
            else
            {
                $result = $this->callService()->regist($entity);
            }
        }

        return new CitrusXhrResult($result);
    }



    /**
     * toggle document_role off
     * 画面ロールの登録
     *
     * @return  CitrusXhrResult
     */
    public function off()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();

        // validate
        if ($this->callFormmap()->validate($this->formmap_toggle_id) > 0)
        {
            $result = false;
        }
        else
        {
            // regist
            /** @var CitrusDatabaseColumn $entity */
            $entity = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_toggle_id);
            $result = $this->callService()->remove($entity->toCondition());
        }

        return new CitrusXhrResult($result);
    }

    /**
     * call service
     *
     * @return  CitrusService
     */
    public function callService()
    {
        if (is_null($this->service) === true)
        {
            $this->service = new CitrusService();
        }
        return $this->service;
    }
}
