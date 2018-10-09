<?php
/*
 * DesInventar - http://www.desinventar.org
 * (c) Corporacion OSSO
 */
namespace DesInventar\Legacy\Model;

use \PDO;
use \Exception;
use \PDOException;

class Record extends Model
{
    const ERR_NO_ERROR = 1;
    const ERR_DEFAULT_ERROR = -1;
    const ERR_OBJECT_NOT_FOUND = -7;
    const ERR_TABLE_LOCKED = -10;

    // Dynamic Objects Variables
    protected $sTableName = 'MyTable';
    protected $sPermPrefix = 'OBJECT';
    protected $conn = null;
    protected $q = null;

    public function __construct($prmSession)
    {
        parent::__construct($prmSession);
        if ($prmSession) {
            $this->q = $prmSession->q;
            $this->setConnection($prmSession->RegionId);
        }
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

    public function getQuery()
    {
        return $this->q;
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
        foreach ($this->explodeFieldList($this->sFieldKeyDef) as $sValue) {
            $oItem = $this->explodeFieldDef($sValue);
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
                ($sFieldType == 'SECTOR') ||
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
        foreach ($this->explodeFieldList($this->sFieldKeyDef) as $sValue) {
            $oItem = $this->explodeFieldDef($sValue);
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
                ($sFieldType == 'SECTOR') ||
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
        if ($prmFieldList === '') {
            return '';
        }
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
            foreach ($this->explodeFieldList($sFieldList) as $sValue) {
                $oItem = $this->explodeFieldDef($sValue);
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
                        ($sFieldType == 'SECTOR') ||
                        ($sFieldType == 'FLOAT'   ) ||
                        ($sFieldType == 'DOUBLE'  ) ||
                        ($sFieldType == 'BOOLEAN' ) ||
                        ($sFieldType == 'CURRENCY') ) {
                    $sQueryItem .= $this->get($sFieldName);
                } else {
                    throw new Exception('Unknown Type : ' . $sFieldType . '/' . $sFieldName);
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
        $iReturn = self::ERR_DEFAULT_ERROR;
        $query = $this->getSelectQuery();
        $sth = $this->conn->prepare($query);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                $iReturn = self::ERR_NO_ERROR;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->conn->rollBack();
            $iReturn = self::ERR_TABLE_LOCKED;
        }
        return $iReturn;
    }

    public function loadRecord($prmTableName, $prmFieldList)
    {
        $iReturn = self::ERR_OBJECT_NOT_FOUND;
        $sQuery = $this->getSelectQuery($prmTableName);
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                foreach ($this->explodeFieldList($prmFieldList) as $sValue) {
                    $oItem = $this->explodeFieldDef($sValue);
                    $sFieldName = $oItem[0];
                    if (array_key_exists($sFieldName, $row)) {
                        $this->set($sFieldName, $row[$sFieldName]);
                    } else {
                        $this->set($sFieldName, '');
                    }
                }
                $iReturn = self::ERR_NO_ERROR;
            }
            $sth->closeCursor();
        } catch (PDOException $e) {
            $this->conn->rollBack();
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
        $iReturn = self::ERR_NO_ERROR;
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
        $iReturn = self::ERR_NO_ERROR;
        $sQuery = $this->getInsertQuery($sTableName);
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            $iReturn = self::ERR_NO_ERROR;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            $iReturn = self::ERR_TABLE_LOCKED;
        }
        return $iReturn;
    }

    public function create($withValidate = 1, $bStrict = 1)
    {
        $iReturn = self::ERR_NO_ERROR;
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
        $iReturn = self::ERR_NO_ERROR;
        $sQuery = $this->getUpdateQuery($prmTableName, $prmFieldList);
        if ($sQuery === '') {
            return self::ERR_NO_ERROR;
        }
        $sth = $this->conn->prepare($sQuery);
        try {
            $this->conn->beginTransaction();
            $sth->execute();
            $this->conn->commit();
            $iReturn = self::ERR_NO_ERROR;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            $iReturn = self::ERR_TABLE_LOCKED;
        }
        return $iReturn;
    }

    public function update($withValidate = 1, $bStrict = 1)
    {
        $iReturn = self::ERR_NO_ERROR;
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
        $iReturn = self::ERR_NO_ERROR;
        if ($this->exist() > 0) {
            $iReturn = $this->update();
        } else {
            $iReturn = $this->insert();
        }
        return $iReturn;
    }

    public function delete($withValidate = true, $bStrict = true)
    {
        $iReturn = self::ERR_NO_ERROR;
        if ($withValidate) {
            $iReturn = $this->validateDelete($bStrict);
        }
        if ($iReturn > 0) {
            $sQuery = $this->getDeleteQuery();
            $sth = $this->conn->prepare($sQuery);
            try {
                $this->conn->beginTransaction();
                $sth->execute();
                $this->conn->commit();
                $iReturn = self::ERR_NO_ERROR;
            } catch (PDOException $e) {
                $this->conn->rollBack();
                $iReturn = self::ERR_TABLE_LOCKED;
            }
        }
        return $iReturn;
    }


    public function getIdWhereQuery()
    {
        $sQuery = '(';
        $i = 0;
        foreach ($this->explodeFieldList($this->sFieldKeyDef) as $sValue) {
            $oItem = $this->explodeFieldDef($sValue);
            $sFieldName = $oItem[0];
            $sFieldType = $oItem[1];
            $quote2 = '"';
            if (in_array($sFieldType, ['INTEGER', 'SECTOR'])) {
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
        if ($this->status->hasError()) {
            $errorCodes = array_keys($this->status->getError());
            return reset($errorCodes);
        }
        if ($this->status->hasWarning() && ($bStrict > 0)) {
            $warningCodes = array_keys($this->status->warning);
            return reset($warningCodes);
        }
        return self::ERR_NO_ERROR;
    }

    public function validateUpdate($bStrict)
    {
        if ($this->status->hasError()) {
            $errorCodes = array_keys($this->status->getError());
            return reset($errorCodes);
        }

        if ($this->status->hasWarning()) {
            if ($bStrict > 0) {
                $warningCodes = array_keys($this->status->warning);
                $iReturn = reset($warningCodes);
            }
        }
        return self::ERR_NO_ERROR;
    }

    public function validateDelete($bStrict)
    {
        return $bStrict > 0;
    }

    public function validateNotNull($ErrCode, $FieldName, $isWarning = false)
    {
        $iReturn = self::ERR_NO_ERROR;
        $Value = $this->get($FieldName);
        $FieldType = $this->getType($FieldName);
        if (in_array($FieldType, ['INTEGER', 'SECTOR'])) {
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
        $iReturn = self::ERR_NO_ERROR;
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
        }

        if ($iReturn < 0) {
            $this->status->addMsg($ErrCode, ' Primary key is not unique');
        }
        return $iReturn;
    }

    public function validateUnique($ErrCode, $prmFieldName, $isWarning = false)
    {
        $iReturn = self::ERR_NO_ERROR;
        $quote1 = '"';
        if (in_array($this->getType($prmFieldName), ['INTEGER', 'SECTOR'])) {
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
                foreach ($this->explodeFieldList($this->sFieldKeyDef) as $sValue) {
                    if ($bFound) {
                        $oItem = $this->explodeFieldDef($sValue);
                        $sFieldName = $oItem[0];
                        $sFieldType = $oItem[1];
                        $bFound = $row[$sFieldName] == $this->get($sFieldName);
                        $i++;
                    }
                }
                if ($bFound) {
                    $iReturn = self::ERR_NO_ERROR;
                } else {
                    $iReturn = $ErrCode;
                }
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->conn->rollBack();
        }
        if ($iReturn < 0) {
            $this->status->addMsg($ErrCode, $prmFieldName . ' value is not unique.', $isWarning);
        }
        return $iReturn;
    }

    public function validateRef($ErrCode, $prmFieldName, $TableName, $FieldDst)
    {
        $quote = in_array($this->getType($prmFieldName), ['INTEGER', 'SECTOR']) ? '' : '"';
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
                $iReturn = self::ERR_NO_ERROR;
            }
            $sth->closeCursor();
        } catch (Exception $e) {
            $this->conn->rollBack();
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
