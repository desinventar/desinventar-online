<?php
namespace DesInventar\Legacy\Model;

use DesInventar\Common\Util;

class Sync extends Record
{
    public function __construct($prmSession)
    {
        $this->sTableName   = "Sync";
        $this->sPermPrefix  = "ADMIN";
        $this->sFieldKeyDef = "SyncId/STRING";
        $this->sFieldDef    = "RegionId/STRING," .
                              "SyncTable/STRING," .
                              "SyncUpload/DATETIME," .
                              "SyncDownload/DATETIME," .
                              "SyncURL/STRING," .
                              "SyncSpec/STRING";
        parent::__construct($prmSession);
        $util = new Util();
        $this->set('SyncId', $util->uuid4());
        $num_args = func_num_args();
        if ($num_args >= 2) {
            $prmSyncId = func_get_arg(1);
            if ($prmSyncId != '') {
                $this->set('SyncId', $prmSyncId);
                $this->load();
            }
        }
    }
}
