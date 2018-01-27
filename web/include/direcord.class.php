<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporacion OSSO
*/
namespace DesInventar\Legacy;

use DIObject;

class DIRecord extends DIObject
{
    // Dynamic Objects Variables
    public $sTableName = 'MyTable';
    public $sPermPrefix = 'OBJECT';
    public $conn = null;
    public $q = null;

    public function __construct($prmSession)
    {
        parent::__construct($prmSession);
        $this->q = $prmSession->q;
        $this->setConnection($prmSession->RegionId);
    }

    public function setConnection($prmDB)
    {
        if ($prmDB == 'core') {
            $this->conn = $this->q->core;
        } else {
            $this->conn = $this->q->dreg;
        }
    }

    public function getTableName()
    {
        return $this->sTableName;
    }

    public function getCreateTable()
    {
        $pk = '';
        $query = '';
        foreach (static::$def as $field_name => $field) {
            if ($query != '') {
                $query .=', ';
            }
            $query .= $field_name;
            $type = $field['type'];
            if ($type == 'VARCHAR') {
                $type .= '(' . $field['size'] . ')';
            }
            $query .= ' '. $type;
            if (isset($field['default'])) {
                $query .= ' DEFAULT ';
                if ($field['type'] == 'VARCHAR') {
                    $query.= "'";
                }
                $query .= $field['default'];
                if ($field['type'] == 'VARCHAR') {
                    $query.= "'";
                }
            }
            if (isset($field['pk'])) {
                if ($pk != '') {
                    $pk .= ',';
                }
                $pk .= $field_name;
            }
        }
        $query = 'CREATE TABLE ' . $this->sTableName . ' (' . $query;
        if ($pk != '') {
            $query .= ', PRIMARY KEY(' . $pk . ')';
        }
        $query .= ')';
        return $query;
    }


    public function getWhereSubQuery()
    {
        $i = 0;
        $sQuery = '(';
        foreach (preg_split('#,#', $this->sFieldKeyDef) as $sKey => $sValue) {
            $oItem = preg_split('#/#', $sValue);
            $sFieldName = $oItem[0];
            $sFieldType = $oItem[1];
            if ($i > 0) {
                $sQuery .= ' AND ';
            }
            $sQuery .= $sFieldName . '=';
            if (($sFieldType == 'STRING'  ) ||
                ($sFieldType == 'VARCHAR' ) ||
                ($sFieldType == 'TEXT'    ) ||
                ($sFieldType == 'DATETIME') ) {
                $sQuery .= '"' . $this->get($sFieldName) . '"';
            }
            if (($sFieldType == 'INTEGER') ||
                ($sFieldType == 'DOUBLE' ) ||
                ($sFieldType == 'CURRENCY') ||
                ($sFieldType == 'BOOLEAN' ) ) {
                $sQuery .= $this->get($sFieldName);
            }
            $i++;
        }
        $sQuery .= ')';
        return $sQuery;
    }

    public function getSelectQuery($prmTableName = '')
    {
        $sTableName = $prmTableName;
        if ($sTableName == '') {
            $sTableName = $this->getTableName();
        }
        $sQuery = 'SELECT * FROM ' . $sTableName;
        $sQuery .= ' WHERE ' . $this->getWhereSubQuery();
        return $sQuery;
    }

    public function getDeleteQuery()
    {
        $sQuery = 'DELETE FROM ' . $this->getTableName();
        $sQuery .= ' WHERE ' . $this->getWhereSubQuery();
        return $sQuery;
    }

    public function getInsertQuery($prmTableName = '')
    {
        $sTableName = $prmTableName;
        if ($sTableName == '') {
            $sTableName = $this->getTableName();
        }
        $i = 0;
        $sQueryFields = '';
        $sQueryValues = '';
        foreach (preg_split('#,#', $this->sFieldKeyDef) as $sKey => $sValue) {
            $oItem = preg_split('#/#', $sValue);
            $sFieldName = $oItem[0];
            $sFieldType = $oItem[1];
            if ($i > 0) {
                $sQueryFields .= ',';
                $sQueryValues .= ',';
            }
            $sQueryFields .= $sFieldName;
            if (($sFieldType == 'STRING'  ) ||
                ($sFieldType == 'VARCHAR' ) ||
                ($sFieldType == 'TEXT'    ) ||
                ($sFieldType == 'DATETIME') ) {
                $sQueryValues .= '"' . $this->get($sFieldName) . '"';
            }
            if (($sFieldType == 'INTEGER')  ||
                ($sFieldType == 'FLOAT'   ) ||
                ($sFieldType == 'DOUBLE'  ) ||
                ($sFieldType == 'CURRENCY') ||
                ($sFieldType == 'BOOLEAN' ) ) {
                $sQueryValues .= $this->get($sFieldName);
            }
            $i++;
        }
        $sQuery = 'INSERT INTO ' . $sTableName . ' (' . $sQueryFields . ') VALUES (' . $sQueryValues . ')';
        return $sQuery;
    }

    public function getUpdateQuery($prmTableName = '', $prmFieldList = '')
    {
        $i = 0;
        if ($this->existField('RecordUpdate')) {
            $this->set('RecordUpdate', gmdate('c'));
        }

        $sTableName = $prmTableName;
        $sFieldList = $prmFieldList;

        if ($sTableName == '') {
            $sTableName = $this->getTableName();
            $sFieldList = $this->sFieldDef;
        }

        $sQueryFields = '';
        $sQueryValues = '';
        $sQuery = 'UPDATE ' . $sTableName . ' SET ';
        if ($sFieldList != '') {
            foreach (preg_split('#,#', $sFieldList) as $sKey => $sValue) {
                $oItem = preg_split('#/#', $sValue);
                $sFieldName = $oItem[0];
                $sFieldType = $oItem[1];
                if ($sFieldType == '') {
                    continue;
                }
                if ($i > 0) {
                    $sQuery .= ',';
                }
                $sQueryItem = $sFieldName . '=';
                if (($sFieldType == 'STRING'  ) ||
                    ($sFieldType == 'VARCHAR' ) ||
                    ($sFieldType == 'TEXT'    ) ||
                    ($sFieldType == 'DATETIME') ||
                    ($sFieldType == 'DATE'    ) ) {
                    $sQueryItem .= '"' . $this->get($sFieldName) . '"';
                } elseif (($sFieldType == 'INTEGER' ) ||
                        ($sFieldType == 'FLOAT'   ) ||
                        ($sFieldType == 'DOUBLE'  ) ||
                        ($sFieldType == 'BOOLEAN' ) ||
                        ($sFieldType == 'CURRENCY') ) {
                    $sQueryItem .= $this->get($sFieldName);
                } else {
                    echo 'Unknown Type : $sFieldType ($sFieldName)<br>';
                }
                $sQuery .= $sQueryItem;
                $i++;
            }
        }
        $sQuery .= ' WHERE ' . $this->getWhereSubQuery();
        return $sQuery;
    }

    public function exist()
    {
        $iReturn = ERR_DEFAULT_ERROR;
        $query = $this->getSelectQuery();
        $sth = $this->conn->prepare($query);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $iReturn = ERR_NO_ERROR;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->conn->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
            $iReturn = ERR_TABLE_LOCKED;
        }
        return $iReturn;
    }

    public function loadRecord($prmTableName, $prmFieldList)
    {
        $iReturn = ERR_OBJECT_NOT_FOUND;
        $sQuery = $this->getSelectQuery($prmTableName);
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $sFields = preg_split('#,#', $prmFieldList);
                foreach ($sFields as $sKey => $sValue) {
                    $oItem = preg_split('#/#', $sValue);
                    $sFieldName = $oItem[0];
                    if (array_key_exists($sFieldName, $row)) {
                        $this->set($sFieldName, $row[$sFieldName]);
                    } else {
                        $this->set($sFieldName, '');
                    }
                }
                $iReturn = ERR_NO_ERROR;
            }
            $sth->closeCursor();
        } catch (PDOException $e) {
            $this->conn->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        return $iReturn;
    }

    public function load()
    {
        $iReturn = $this->loadRecord($this->getTableName(), $this->sFieldKeyDef . ',' . $this->sFieldDef);
        $this->oOldField = $this->oField;
        return $iReturn;
    }

    public function insert($withValidate = 1, $bStrict = 1)
    {
        $iReturn = ERR_NO_ERROR;
        $this->status->clear();
        $bValidate = $withValidate;
        if ($withValidate > 0) {
            $iReturn = $this->validateCreate($bStrict);
            if ($iReturn > 0) {
                $iReturn = $this->validateUpdate($bStrict);
                $bValidate = 0;
            }
        }
        if ($iReturn > 0) {
            $iReturn = $this->create($bValidate, $bStrict);
            if ($iReturn > 0) {
                $iReturn = $this->update($bValidate, $bStrict);
            }
        }
        return $iReturn;
    }

    public function createRecord($sTableName)
    {
        $iReturn = ERR_NO_ERROR;
        $sQuery = $this->getInsertQuery($sTableName);
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            $iReturn = ERR_NO_ERROR;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
            $iReturn = ERR_TABLE_LOCKED;
        }
        return $iReturn;
    }

    public function create($withValidate = 1, $bStrict = 1)
    {
        $iReturn = ERR_NO_ERROR;
        if ($withValidate > 0) {
            $iReturn = $this->validateCreate($bStrict);
        }
        if ($iReturn > 0) {
            $iReturn = $this->createRecord($this->getTableName());
        }
        return $iReturn;
    }

    public function updateRecord($prmTableName, $prmFieldList)
    {
        $iReturn = ERR_NO_ERROR;
        $sQuery = $this->getUpdateQuery($prmTableName, $prmFieldList);
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            $iReturn = ERR_NO_ERROR;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
            $iReturn = ERR_TABLE_LOCKED;
        }
        return $iReturn;
    }

    public function update($withValidate = 1, $bStrict = 1)
    {
        $iReturn = ERR_NO_ERROR;
        $this->status->clear();
        if ($withValidate > 0) {
            $iReturn = $this->validateUpdate($bStrict);
        }
        if ($iReturn > 0) {
            $iReturn = $this->updateRecord($this->getTableName(), $this->sFieldDef);
        }
        if ($iReturn > 0) {
            $this->oOldField = $this->oField;
        }
        return $iReturn;
    }

    public function save()
    {
        $iReturn = ERR_NO_ERROR;
        if ($this->exist() > 0) {
            $iReturn = $this->update();
        } else {
            $iReturn = $this->insert();
        }
        return $iReturn;
    }

    public function delete($withValidate = true, $bStrict = true)
    {
        $iReturn = ERR_NO_ERROR;
        if ($withValidate) {
            $iReturn = validateDelete($bStrict);
        }
        if ($iReturn > 0) {
            $sQuery = $this->getDeleteQuery();
            $sth = $this->conn->prepare($sQuery);
            try {
                $this->conn->beginTransaction();
                $sth->execute();
                $this->conn->commit();
                $iReturn = ERR_NO_ERROR;
            } catch (PDOException $e) {
                $this->conn->rollBack();
                showErrorMsg(debug_backtrace(), $e, '');
                $iReturn = ERR_TABLE_LOCKED;
            }
        }
        return $iReturn;
    }


    public function getIdWhereQuery()
    {
        $sQuery = '(';
        $i = 0;
        $sFields = preg_split('#,#', $this->sFieldKeyDef);
        foreach ($sFields as $sKey => $sValue) {
            $oItem = preg_split('#/#', $sValue);
            $sFieldName = $oItem[0];
            $sFieldType = $oItem[1];
            $quote2 = '"';
            if ($sFieldType == 'INTEGER') {
                $quote2 = '';
            }
            if ($i > 0) {
                $sQuery .= ' AND ';
            }
            $sQuery .= $sFieldName . '=' . $quote2 . $this->get($sFieldName) . $quote2;
            $i++;
        }
        $sQuery .= ')';
        return $sQuery;
    }

    public function validateCreate($bStrict)
    {
        $iReturn = ERR_NO_ERROR;
        if ($this->status->hasError()) {
            $iReturn = reset(array_keys($this->status->error));
        } elseif ($this->status->hasWarning()) {
            $iReturn = ERR_NO_ERROR;
            if ($bStrict > 0) {
                $iReturn = reset(array_keys($this->status->warning));
            }
        }
        return $iReturn;
    }

    public function validateUpdate($bStrict)
    {
        $iReturn = ERR_NO_ERROR;
        if ($this->status->hasError()) {
            $iReturn = reset(array_keys($this->status->error));
        } elseif ($this->status->hasWarning()) {
            $iReturn = ERR_NO_ERROR;
            if ($bStrict > 0) {
                $iReturn = reset(array_keys($this->status->warning));
            }
        }
        return $iReturn;
    }

    public function validateDelete($bStrict)
    {
        return 1;
    }

    public function validateNotNull($ErrCode, $FieldName, $isWarning = false)
    {
        $iReturn = ERR_NO_ERROR;
        $Value = $this->get($FieldName);
        $FieldType = $this->getType($FieldName);
        if ($FieldType == 'INTEGER') {
            $Value = (int)$Value;
        } else {
            if ($Value == '') {
                $iReturn = $ErrCode;
            }
        }
        if ($iReturn < 0) {
            $this->status->addMsg($ErrCode, $FieldName . ' is null', $isWarning);
        }
        return $iReturn;
    }

    public function validatePrimaryKey($ErrCode)
    {
        $iReturn = ERR_NO_ERROR;
        $quote1 = '"';
        $sQuery = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ' . $this->getIdWhereQuery();
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $iReturn = $ErrCode;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->conn->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }

        if ($iReturn < 0) {
            $this->status->addMsg($ErrCode, ' Primary key is not unique');
        }
        return $iReturn;
    }

    public function validateUnique($ErrCode, $prmFieldName, $isWarning = false)
    {
        $iReturn = ERR_NO_ERROR;
        $quote1 = '"';
        if ($this->getType($prmFieldName) == 'INTEGER') {
            $quote1 = '';
        }
        $sQuery = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ' .
            $prmFieldName . '=' . $quote1 . $this->get($prmFieldName) . $quote1;
        // Validate is LangIsoCode is defined and Modify the query when needed
        if ($this->existField('LangIsoCode')) {
            $sQuery .= ' AND LangIsoCode="' . $this->get('LangIsoCode') . '"';
        }
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                // Check if it's me !!
                $bFound = true;
                $i = 0;
                $sFields = preg_split('#,#', $this->sFieldKeyDef);
                foreach ($sFields as $sKey => $sValue) {
                    if ($bFound) {
                        $oItem = preg_split('#/#', $sValue);
                        $sFieldName = $oItem[0];
                        $sFieldType = $oItem[1];
                        $bFound = $row[$sFieldName] == $this->get($sFieldName);
                        $i++;
                    }
                }
                if ($bFound) {
                    $iReturn = ERR_NO_ERROR;
                } else {
                    $iReturn = $ErrCode;
                }
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->conn->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        if ($iReturn < 0) {
            $this->status->addMsg($ErrCode, $prmFieldName . ' value is not unique.', $isWarning);
        }
        return $iReturn;
    }

    public function validateRef($ErrCode, $prmFieldName, $TableName, $FieldDst)
    {
        $quote = $this->getType($prmFieldName) == 'INTEGER' ? '' : '"';
        $Value = $this->get($prmFieldName);
        $sQuery = trim('
            SELECT ' . $FieldDst . '
            FROM ' . $TableName . '
            WHERE ' . $FieldDst . '=' . $quote . $Value .  $quote . '
        ');
        $iReturn = $ErrCode;
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $iReturn = ERR_NO_ERROR;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->conn->rollBack();
            showErrorMsg(debug_backtrace(), $e, '');
        }
        if ($iReturn < 0) {
            $this->status->addMsg(
                $ErrCode,
                $prmFieldName . ' reference to table ' . $TableName . ' is invalid (' . $Value . ')'
            );
        }
        return $iReturn;
    }
}
