<?php
/*
 * DesInventar - http://www.desinventar.org
 * (c) Corporacion OSSO
 */
namespace DesInventar\Legacy\Model;

use Aura\SqlQuery\QueryFactory;

class User extends Record
{
    public function __construct($prmSession)
    {
        $this->sTableName   = "User";
        $this->sPermPrefix  = "ADMIN";
        $this->sFieldKeyDef = "UserId/STRING";
        $this->sFieldDef    =
            "UserEMail/STRING," .
            "UserPasswd/STRING," .
            "UserFullName/STRING," .
            "Organization/STRING," .
            "CountryIso/STRING," .
            "UserCity/STRING," .
            "UserCreationDate/DATETIME," .
            "UserNotes/STRING," .
            "UserActive/BOOLEAN";
        parent::__construct($prmSession);
        $num_args = func_num_args();
        $this->setConnection("core");
        if ($num_args >= 2) {
            $prmUserId = func_get_arg(1);
            if ($prmUserId != '') {
                $this->set('UserId', $prmUserId);
                $this->load();
            }
        }
    }

    public function getSelectQuery($prmTableName = '')
    {
        $query = parent::getSelectQuery($prmTableName);
        $query .= " OR (UserNotes LIKE '%(UserName=" . $this->get('UserId') . ")%')";
        return $query;
    }

    public function updatePasswd($id, $passwd)
    {
        $queryFactory = new QueryFactory('sqlite');
        $update = $queryFactory->newUpdate();
        $update
            ->table($this->sTableName)
            ->cols(array('UserPasswd'))
            ->where('UserId = :UserId')
            ->bindValues(array(
                'UserPasswd' => $passwd,
                'UserId' => $id
            ));
        $sth = $this->conn->prepare($update->getStatement());
        $sth->execute($update->getBindValues());
        return true;
    }

    public function getInfo()
    {
        $info = $this->oField['info'];
        unset($info['UserPasswd']);
        return $info;
    }
}
