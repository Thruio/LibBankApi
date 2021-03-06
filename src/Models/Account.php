<?php

namespace Thru\BankApi\Models;

use Thru\ActiveRecord\ActiveRecord;

/**
 * Class Account
 * @var $account_id integer
 * @var $account_holder_id integer
 * @var $name text
 * @var $balance_inverted ENUM("Yes","No")
 * @var $created date
 * @var $updated date
 * @var $last_check date
 */
class Account extends ActiveRecord
{

    protected $_table = "accounts";

    public $account_id;
    public $account_holder_id;
    public $name;
    public $balance_inverted = "No";
    public $created;
    public $updated;
    public $last_check;

    private $_account_holder;
    private $_balance;

  /**
   * @param $accountHolder AccountHolder
   * @param $name
   * @return Account
   */
    public static function FetchOrCreateByName(AccountHolder $accountHolder, $name)
    {
        $account = Account::factory()
        ->search()
        ->where('account_holder_id', $accountHolder->account_holder_id)
        ->where('name', $name)
        ->execOne();
        if (!$account) {
            $account = new Account();
            $account->account_holder_id = $accountHolder->account_holder_id;
            $account->name = $name;
            $account->save();
        }
        return $account;
    }

    public function save($automatic_reload = true)
    {
        $this->updated = date("Y-m-d H:i:s");
        if (!$this->created) {
            $this->created = date("Y-m-d H:i:s");
        }
        if (!$this->last_check) {
            $this->last_check = date("Y-m-d H:i:s", 0);
        }
        parent::save($automatic_reload);
    }

  /**
   * @return AccountHolder
   */
    public function getAccountHolder()
    {
        if (!$this->_account_holder) {
            $this->_account_holder = AccountHolder::search()->where('account_holder_id', $this->account_holder_id)->execOne();
        }
        return $this->_account_holder;
    }

  /**
   * @return Balance
   */
    public function getBalance()
    {
        if (!$this->_balance) {
            $this->_balance = Balance::search()->where('account_id', $this->account_id)->order('run_id', 'DESC')->execOne();
        }
        return $this->_balance;
    }
}
