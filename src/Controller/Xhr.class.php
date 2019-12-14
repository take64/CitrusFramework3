<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2017, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Controller;

use Citrus\CitrusException;
use Citrus\Configure;
use Citrus\Configure\ConfigureException;
use Citrus\Database\Column;
use Citrus\Document\Pager;
use Citrus\Formmap;
use Citrus\Logger;
use Citrus\Message;
use Citrus\Message\Item;
use Citrus\Service;
use Citrus\Session;
use Citrus\Sqlmap\Condition;
use Citrus\Sqlmap\SqlmapException;
use Citrus\Xhr\Element;
use Citrus\Xhr\Result;

/**
 * Xhr通信
 */
class Xhr
{
    /** @var Formmap citrus formmap object */
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

    /** @var Service service  */
    protected $service = null;

    /** @var array remove column summaries is empty */
    protected $remove_column_summaries_is_empty = [
        'count', 'sum', 'avg', 'max', 'min', 'name', 'id',
    ];

    /** @var array remove column */
    protected $remove_column = [
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
            $actionName = Session::$router->action;

            $result = new Element();
            $this->initialize();
            $result->results = $this->$actionName();
            $this->release();
            $result->messages = Message::callItems();
            $response = $result;
        }
        catch (CitrusException $e)
        {
            Message::addError($e->getMessage());
            $result = new Element();
            $message = '実行時エラーが検出されました。';
            $result->exceptions = [new CitrusException($message)];
            $result->messages = [new Item($message, Item::TYPE_ERROR)];
            $result->results = new Result();
            Logger::error($result);
            $response = $result;
            Message::removeAll();
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
     * call faces summary list
     * サマリリストの取得
     *
     * @return Result
     * @throws CitrusException
     */
    public function facesSummaries(): Result
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace . '.php');
        $this->callFormmap()->bind();
        /** @var Column|Condition $condition */
        $condition = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_call_id);
        $condition->toLike($this->search_column_to_like);

        // validate
        if ($this->callFormmap()->validate($this->formmap_call_id) > 0)
        {
            $result = new Result();
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
                    $one->remove($this->remove_column);
                    $one->removeIsEmpty($this->remove_column_summaries_is_empty);
                    $one->null2blank();
                }
            }

            $result = new Result($list);
            $result->pager = new Pager($condition->page, $count, $condition->limit, 7);
        }

        return $result;
    }



    /**
     * call summary record
     * サマリの取得
     *
     * @return Result
     * @throws SqlmapException
     */
    public function facesDetail(): Result
    {
        // condition
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();
        /** @var Column|Condition $condition */
        $condition = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_view_id);

        // call detail
        $detail = $this->callService()->facesDetail($condition);

        // modify
        $detail->remove($this->remove_column);
        $detail->removeIsEmpty($this->remove_column_view_is_empty);
        $detail->null2blank();

        return new Result($detail->properties());
    }



    /**
     * regist item
     * の登録
     *
     * @return Result
     * @throws CitrusException
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
            /** @var Column $entity */
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

        return new Result([$result]);
    }



    /**
     * remove & item
     * の削除
     *
     * @return Result
     * @throws SqlmapException
     */
    public function remove()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();

        // remove
        /** @var Column $entity */
        $entity = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_edit_id);
        return new Result([$this->callService()->remove($entity->getCondition())]);
    }



    /**
     * call summary list
     * サマリリストの取得
     *
     * @return Result
     * @throws SqlmapException
     */
    public function selections()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();

        // condition
        /** @var Column|Condition $condition */
        $condition = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_call_id);
        $condition->toLike($this->search_column_to_like);

        // condition
        if (empty($condition->orderby) === true)
        {
            $condition->orderby = $this->default_orderby;
        }
        $condition->pageLimit();

        // call list
        $list = $this->callService()->facesSelections($condition);
        $count = 0;

        // data exist
        if (empty($list) === false)
        {
            // call count
            $count = $this->callService()->count($condition);
            // modify
            foreach ($list as &$one)
            {
                $one->remove([
                    'status',
                    'schema',
                    'resisted_at',
                    'modified_at',
                    'condition',
                    ]);
                $one->null2blank();
            }
        }

        $result = new Result($list);
        $result->pager = new Pager($condition->page, $count, $condition->limit, 7);

        return $result;
    }



    /**
     * call summary list
     * サマリリストの取得
     *
     * @return Result
     * @throws SqlmapException
     */
    public function suggests()
    {
        // get form data
        $this->callFormmap()->load($this->formmap_namespace.'.php');
        $this->callFormmap()->bind();

        // call
        /** @var Column|Condition $condition */
        $condition = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_suggest_id);
        if (empty($condition->orderby) === true)
        {
            $condition->orderby = $this->suggest_orderby;
        }
        $condition->pageLimit(1, $condition->limit);
        $condition->toLike($this->search_column_to_like);
        $list = $this->callService()->names($condition);

        $result = [];
        foreach ($list as $one)
        {
            $result[] = ['label' => $one->name, 'value' => $one->id];
        }

        return new Result($result);
    }



    /**
     * toggle on
     *
     * @return Result
     * @throws CitrusException
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
            /** @var Column $entity */
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

        return new Result([$result]);
    }



    /**
     * toggle off
     *
     * @return Result
     * @throws CitrusException
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
            /** @var Column $entity */
            $entity = $this->callFormmap()->generate($this->formmap_namespace, $this->formmap_toggle_id);
            $result = $this->callService()->remove($entity->toCondition());
        }

        return new Result([$result]);
    }



    /**
     * call service
     *
     * @return  Service
     */
    public function callService()
    {
        if (is_null($this->service) === true)
        {
            $this->service = new Service();
        }
        return $this->service;
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
     * @return Formmap
     * @throws ConfigureException
     */
    protected function callFormmap(): Formmap
    {
        if (is_null($this->formmap) === true)
        {
            $this->formmap = Formmap::getInstance()
                ->loadConfigures(Configure::$CONFIGURES);
        }
        return $this->formmap;
    }
}
