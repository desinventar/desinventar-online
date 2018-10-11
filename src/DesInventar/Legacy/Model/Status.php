<?php
/*
 * DesInventar - http://www.desinventar.org
 * (c) Corporacion OSSO
 */
namespace DesInventar\Legacy\Model;

class Status
{
    const ERR_NO_ERROR = 1;

    protected $status = 0;
    protected $error = [];
    protected $warning = [];

    public function __construct()
    {
        $this->status  = self::ERR_NO_ERROR;
        $this->clear();
    }

    public function clear()
    {
        $this->error = [];
        $this->warning = [];
    }

    public function getError()
    {
        return $this->error;
    }

    public function addMsg($errCode, $errMsg, $isWarning = false)
    {
        if ($isWarning) {
            return $this->addWarning($errCode, $errMsg);
        }
        return $this->addError($errCode, $errMsg);
    }

    public function addError($errCode, $errMsg)
    {
        $this->error[$errCode] = $errMsg;
        $this->status = $errCode;
    }

    public function addWarning($errCode, $errMsg)
    {
        $this->warning[$errCode] = $errMsg;
        $this->status = 0;
    }

    public function hasError($errCode = 0)
    {
        $bAnswer = false;
        if (count($this->error) > 0) {
            $bAnswer = true;
        }
        if ($errCode != 0) {
            $bAnswer = array_key_exists($errCode, $this->error);
        }
        return $bAnswer;
    }

    public function hasWarning($errCode = 0)
    {
        $bAnswer = false;
        if (count($this->warning) > 0) {
            $bAnswer = true;
        }
        if ($errCode != 0) {
            $bAnswer = array_key_exists($errCode, $this->warning);
        }
        return $bAnswer;
    }
}
