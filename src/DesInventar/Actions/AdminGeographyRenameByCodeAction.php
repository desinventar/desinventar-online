<?php

namespace DesInventar\Actions;

use Exception;
use PDO;

use DesInventar\Models\Disaster;
use DesInventar\Models\Geography;
use DesInventar\Legacy\Model\GeographyItem;

class AdminGeographyRenameByCodeAction
{
    protected $pdo = null;
    protected $logger = null;

    public function __construct($pdo, $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    public function execute($code, $newCode)
    {
        $geography = $this->findGeographyByCode($code);
        if (!$geography) {
            throw new Exception('Cannot find geography item with code: ' . $code);
        }
        $oldParentCode = $this->getParentCodeById($geography['GeographyId'], $geography['GeographyLevel']);

        // Rename geography
        $this->renameGeographyByCode($code, $newCode, $oldParentCode, $geography['GeographyId']);
        // Find any children (next level only)
        $children = (new Geography($this->pdo, $this->logger))->findChildren($geography['GeographyId']);
        // Recurse rename each child (should attempt to rename the next level)
        foreach ($children as $child) {
            $newChildCode = $newCode . substr($child['code'], strlen($newCode));
            $this->renameGeographyByCode($child['code'], $newChildCode, $code, $child['id']);
        }
    }

    public function renameGeographyByCode($code, $newCode, $oldParentCode, $oldId)
    {
        $this->logger->debug("renameGeographyByCode called  {$code} => {$newCode}");
        if ($this->findGeographyByCode($newCode)) {
            throw new Exception('Geography item already exists for code: ' . $newCode);
        }
        $newParentCode = substr($newCode, 0, strlen($oldParentCode));
        if ($oldParentCode === $newParentCode) {
            // Rename geography in the same level, no id change just change the code
            $this->logger->debug("Rename geography in the same level: ${code} => ${newCode}");
            $geography = new Geography($this->pdo, $this->logger);
            return
                $geography->update($oldId, ['GeographyCode' => $newCode]) &&
                $geography->updateFQNameByCode($newCode);
        }
        $newParent = $this->findGeographyByCode($newParentCode);
        $newParentId = $newParent['GeographyId'];
        if (!$newParent) {
            throw new Exception("Cannot find geography parent with code: ${newParentCode}");
        }
        $newId = (new GeographyItem($this->pdo))->buildGeographyId($newParentId);
        $this->logger->debug("Rename geography between levels: " .
            "{$code}/{$oldParentCode}/{$oldId} " .
            "{$newCode}/{$newParentCode}/{$newParentId}/{$newId}");
        $this->pdo->beginTransaction();
        $geography = new Geography($this->pdo, $this->logger);
        $geography->update(
            $oldId,
            ['GeographyCode' => $newCode, 'GeographyId' => $newId]
        );
        $geography->updateFQNameByCode($newCode);
        (new Disaster($this->pdo, $this->logger))->updateGeography($oldId, $newId);
        $this->pdo->commit();
        return $oldParentCode;
    }

    public function findGeographyByCode($code)
    {
        return (new Geography($this->pdo, $this->logger))->findByCode($code);
    }

    public function getParentCodeById($id, $level)
    {
        $parentId = substr($id, 0, intval($level) * 5);
        if (!$parentId) {
            return '';
        }
        $geography = (new Geography($this->pdo, $this->logger))->findById($parentId);
        if (!$geography) {
            throw new Exception('Cannot find geography with id: ' . $parentId);
        }
        return $geography['GeographyCode'];
    }
}
