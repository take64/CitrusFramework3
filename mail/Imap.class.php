<?php
/**
 * @copyright   Copyright 2018, Citrus/besidesplus All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Mail;


use Citrus\Mail\Header\CitrusMailHeaderItem;
use Citrus\Mail\Imap\CitrusMailImapBox;
use Citrus\Mail\Imap\CitrusMailImapQuota;
use Citrus\Mail\Search\CitrusMailSearchCondition;

class CitrusMailImap
{
    /** @var resource IMAP */
    public $imap_handle;

    /** @var CitrusMailImapAccount アカウント */
    public $account;



    /**
     * CitrusMailImap constructor.
     *
     * @param CitrusMailImapAccount $account アカウント情報
     */
    public function __construct(CitrusMailImapAccount $account)
    {
        $this->account = $account;
    }



    /**
     * destructor.
     */
    public function __destruct()
    {
        $this->close();
    }



    /**
     * 指定フォルダのハンドルのオープン
     *
     * @param string $folder_name 指定フォルダ
     * @return bool|resource
     */
    public function open(string $folder_name = '')
    {
        // メールボックス名
        $mailbox_name = $this->callMailBox($folder_name);
        // メールボックスが UTF-8 だった場合は UTF7-IMAP に変更
        $mailbox_name = mb_convert_encoding($mailbox_name, 'UTF7-IMAP');

        // resource IMAP のオープン
        if (is_null($this->imap_handle) === true)
        {
            $this->imap_handle = imap_open(
                $mailbox_name,
                $this->account->username,
                $this->account->password
            );
        }
        // すでにオープンされている場合は、再度オープンする(フォルダ指定されている場合だけ)
        else if (empty($folder_name) === false)
        {
            @imap_reopen(
                $this->imap_handle,
                $mailbox_name
            );
            // エラースタックをクリアする
            imap_errors();
        }
        return $this->imap_handle;
    }



    /**
     * ハンドルのクローズ
     */
    public function close()
    {
        if (is_null($this->imap_handle) === false)
        {
            imap_close($this->imap_handle);
            $this->imap_handle = null;
        }
    }



    /**
     * フォルダリストの取得
     *
     * @param string $folder_name フォルダ
     * @param string $pattern     検索パターン
     * @return string[]
     */
    public function folders(string $folder_name = '', string $pattern = '*')
    {
        $result = [];

        $folders = imap_list(
            $this->open(),
            $this->callMailBox($folder_name),
            $pattern
        );
        foreach ($folders as $folder)
        {
            $result[] = mb_convert_encoding($folder, 'UTF-8', 'UTF7-IMAP');
        }

        return $result;
    }



    /**
     * メールボックスクォータの取得
     *
     * @return CitrusMailImapQuota
     */
    public function quota()
    {
        return new CitrusMailImapQuota(imap_get_quotaroot($this->open(), 'INBOX'));
    }



    /**
     * フォルダ情報の取得
     *
     * @param string $folder_name フォルダ
     * @return CitrusMailImapBox
     */
    public function folderDetail(string $folder_name = '')
    {
        return new CitrusMailImapBox(imap_mailboxmsginfo($this->open($folder_name)));
    }



    /**
     * サーバー情報を抜いたフォルダ名の取得
     *
     * @param string $folder_name
     * @return string
     */
    public function folderPureName(string $folder_name)
    {
        return str_replace(
            sprintf('{%s}', $this->mail_server),
            '',
            $folder_name
        );
    }



    /**
     * 指定フォルダのメール概要を取得する
     *
     * @param CitrusMailSearchCondition|null $condition 検索条件
     * @return CitrusMailHeaderItem[]
     */
    public function mailSummaries(CitrusMailSearchCondition $condition = null)
    {
        // 検索条件
        $condition = $condition ?: (new CitrusMailSearchCondition());

        // メールボックスハンドル
        $handle = $this->open($condition->folder_path);

        // 検索シーケンス
        $sequence = null;
        // 検索オプション
        $options = 0;

        // msgno で検索
        if (empty($condition->msgnos) === false)
        {
            $sequence = implode(',', $condition->msgnos);
        }
        // uid で検索
        else if (empty($condition->uids) === false)
        {
            $sequence = implode(',', $condition->uids);
            $options = FT_UID;
        }
        // 全て取得
        else
        {
            $mailBox = new CitrusMailImapBox(imap_check($handle));
            $sequence = sprintf('1:%d', $mailBox->quantity);
        }

        // メール取得
        $fetches = imap_fetch_overview($handle, $sequence, $options);
        // オブジェクト化
        $results = [];
        foreach ($fetches as $fetch)
        {
            $results[] = new CitrusMailHeaderItem($fetch);
        }
        return $results;
    }



    /**
     * メール移動
     *
     * @param CitrusMailSearchCondition $condition      検索条件
     * @param string                    $folder_path_to 移動先フォルダパス
     */
    public function move(CitrusMailSearchCondition $condition, string $folder_path_to)
    {
        // エンコード変更
        $folder_path_to = mb_convert_encoding($folder_path_to, 'UTF7-IMAP', 'UTF-8');

        // 条件がない場合は処理しない
        if (empty($condition->uids) === true && empty($condition->msgnos) === true)
        {
            return ;
        }

        // メールボックスハンドル
        $handle = $this->open($condition->folder_path);

        // 検索シーケンス
        $sequence = null;
        // 検索オプション
        $options = 0;

        // msgno で検索
        if (empty($condition->msgnos) === false)
        {
            $sequence = implode(',', $condition->msgnos);
        }
        // uid で検索
        else if (empty($condition->uids) === false)
        {
            $sequence = implode(',', $condition->uids);
            $options = CP_UID;
        }
        // 全て移動
        else
        {
            $mailBox = new CitrusMailImapBox(imap_check($handle));
            $sequence = sprintf('1:%d', $mailBox->quantity);
        }

        // 移動実行
        imap_mail_move($handle, $sequence, $folder_path_to, $options);
        // 移動したメールの削除
        imap_expunge($handle);
    }



    /**
     * フォルダの作成
     *
     * @param string $folder_path 作成フォルダパス
     */
    public function createFolder(string $folder_path)
    {
        // 完全なメールフォルダパス
        $mailbox_ref = $this->callMailBox($folder_path);

        // メールフォルダの作成
        imap_create($this->open(), mb_convert_encoding($mailbox_ref, 'UTF7-IMAP', 'UTF-8'));
    }



    /**
     * メールサーバー文字列の取得
     *
     * @param string $folder_name 指定フォルダ名
     * @return string
     */
    private function callMailBox(string $folder_name = '')
    {
        if ($folder_name === '')
        {
            return sprintf('{%s}', $this->account->mail_server);
        }

        $decoration_mailbox_ref = $this->callMailBox();
        return sprintf('%s%s',
            $decoration_mailbox_ref,
            str_replace($decoration_mailbox_ref, '', $folder_name)
        );
    }
}