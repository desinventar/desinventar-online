<?php
namespace DesInventar\Legacy;

use DesInventar\Legacy\Model\DisasterImport;
use DesInventar\Legacy\Model\Event;
use DesInventar\Legacy\Model\Cause;
use DesInventar\Legacy\Model\GeographyItem;
use \Exception;

class DIImport
{
    protected $session = null;
    protected $logger = null;
    protected $params = null;
    protected $fields = [];
    protected $geography = [];
    protected $event = [];
    protected $cause = [];
    protected $begintime = [];
    protected $serial = [];
    protected $values = [];
    protected $fixedValues = [];
    protected $defaultParams = [
        'headerCount' => 3,
        'offset' => 0,
        'lineCount' => 1000000,
        'geography' => [],
        'event' => [],
        'cause' => [],
        'begintime' => [],
        'serial' => [],
        'values' => [],
        'fields' => []
    ];

    public function __construct($prmSessionId, $logger, $params)
    {
        $this->session = $prmSessionId;
        $this->logger = $logger;
        $this->params = array_merge($this->defaultParams, $params);
        $this->fields = $this->getFieldDef($this->params['fields']);
        $this->geography = $this->params['geography'];
        $this->event = $this->params['event'];
        $this->cause = $this->params['cause'];
        $this->begintime = $this->params['begintime'];
        $this->serial = $this->params['serial'];
        $this->fixedValues = $this->params['values'];
    }

    public static function loadParamsFromFile($fileName)
    {
        if (!file_exists($fileName)) {
            throw new Exception('Import Error: Cannot find field definition file');
        }
        $json = (string) file_get_contents($fileName);
        $params = json_decode($json, true);
        if (empty($params)) {
            throw new Exception('Import Error: Cannot import field definitions from file');
        }
        return $params;
    }

    public function getFieldDef($fields)
    {
        $def = [];
        $lastIndex = 0;
        foreach ($fields as $field) {
            $fieldName = $field[0];
            $comment = isset($field[1]) ?  $field[1] : '';
            $index = [ $lastIndex ];
            if (count($field) > 2) {
                $index = array_slice($field, 2);
            }
            $lastIndex = end($index) + 1;
            $def[$fieldName] = [
                'comment' => $comment,
                'index' => array_merge(
                    isset($def[$fieldName]['index']) ? $def[$fieldName]['index'] : [],
                    $index
                )
            ];
        }
        $list = [];
        foreach ($def as $fieldName => $values) {
            $list[] = [
                'name'=> $fieldName,
                'comment' => $values['comment'],
                'index' => $values['index']
            ];
        }
        return $list;
    }

    public function validateFromCSV($fileName, $objectType)
    {
        return $this->importFromCSV($fileName, $objectType);
    }

    public function findGeographyIdByName($fullName, $separator)
    {
        $geographyId = '';
        $names = explode($separator, $fullName);
        if (!$names) {
            $names= [];
        }
        foreach ($names as $name) {
            $geographyId = GeographyItem::getIdByName($this->session, $name, $geographyId);
        }
        return $geographyId;
    }

    public function getGeographyId($values)
    {
        $type = !empty($this->geography['type']) ? $this->geography['type'] : 'code';
        switch ($type) {
            case 'fullname':
                $name = $values[$this->geography['index']];
                $geographyId = $this->findGeographyIdByName($name, $this->geography['separator']);
                if (empty($geographyId)) {
                    throw new Exception('Error trying to match geography name: ' . $name);
                }
                return $geographyId;
            case 'code':
            default:
                $code = $values[$this->geography['code']];
                if (isset($this->geography['width']) &&
                    isset($this->geography['minlength']) &&
                    strlen($code) >= $this->geography['minlength']
                ) {
                    $code = str_pad($code, $this->geography['width'], '0', STR_PAD_LEFT);
                }
                $geographyId = GeographyItem::getIdByCode($this->session, $code);
                if (empty($geographyId)) {
                    throw new Exception('Error trying to match geography code: ' . $code);
                }
                return $geographyId;
        }
    }

    public function getEventId($values)
    {
        $type = !empty($this->event['type']) ? $this->event['type'] : 'name';
        if ($type === 'fixed') {
            return $this->event['id'];
        }

        // search eventId by name
        $name = $this->getName($values, $this->event);
        $eventId = Event::getIdByName($this->session, $name);
        if (empty($eventId)) {
            throw new Exception('Error trying to match event: ' . $name);
        }
        return $eventId;
    }

    public function getCauseId($values)
    {
        $type = !empty($this->cause['type']) ? $this->cause['type'] : 'name';

        if ($type === 'fixed') {
            return $this->cause['id'];
        }

        // search causeId by name
        $name = $this->getName($values, $this->cause);
        $causeId = $name == '' ? 'UNKNOWN' : Cause::getIdByName($this->session, $name);
        if (empty($causeId)) {
            throw new Exception('Error trying to match cause: ' . $name);
        }
        return $causeId;
    }

    public function getDate($value)
    {
        if (strlen($value) < 10) {
            return '';
        }
        if (substr($value, 0, 4) === 'Asin') {
            return '';
        }
        return substr($value, 0, 10);
    }

    public function getDisasterBeginTime($values)
    {
        $datetime = $values[$this->begintime['column']];
        return $this->getDate($datetime);
    }

    public function getDisasterSerial($year, $values)
    {
        $number = $values[$this->serial['column']];
        return $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function getName($values, $data)
    {
        $name = trim($values[$data['name']]);
        if (array_key_exists($name, $data['fixes'])) {
            $name = $data['fixes'][$name];
        }
        return $name;
    }

    public function validateOnCreateOrUpdate($disaster)
    {
        $bExist = $disaster->exist();
        if ($bExist < 0) {
            return $disaster->validateCreate(1);
        }
        return $disaster->validateUpdate(1);
    }

    public function importFromCSV($prmFileCSV, $prmImport)
    {
        $last_line = $this->params['lineCount'];
        $skipLines = (int) $this->params['headerCount'] + $this->params['offset'];

        $last_line = $last_line + $skipLines + 1;
        $fh = fopen($prmFileCSV, 'r');
        if (!$fh) {
            throw new Exception('Cannot open CSV file');
        }
        $line = 1;
        while ($line <= $skipLines) {
            $a = fgetcsv($fh, 0, ',');
            $line++;
        }
        while ((! feof($fh)) && ($line < $last_line)) {
            $a = fgetcsv($fh, 0, ',');

            if (!is_array($a) || count($a) < 2) {
                continue;
            }
            $aLength = count($a);
            for ($i = 0; $i < $aLength; $i++) {
                $a[$i] = trim($a[$i]);
            }

            $DisasterSerial = '';
            try {
                $disaster = $this->getImportObjectFromArray($a);
                $DisasterSerial = $disaster->get('DisasterSerial');
                $this->logger->debug(sprintf('%d %s', $line, $DisasterSerial));
            } catch (\Exception $e) {
                fprintf(STDERR, '%4d,%s,%s' . "\n", $line, $DisasterSerial, $e->getMessage());
                $line++;
                continue;
            }
            $line++;

            // Validate Effects and Save as DRAFT if needed
            if ($disaster->validateEffects(-61, 0) < 0) {
                $disaster->set('RecordStatus', 'DRAFT');
            }

            $bExist = $disaster->exist();
            $r = $this->validateOnCreateOrUpdate($disaster);
            if ($r < 0) {
                fprintf(STDERR, "Error en validación serial: %s Error: %d\n", $DisasterSerial, $r);
            }

            if (($line > 0) && (($line % 100) == 0)) {
                $this->logger->info(sprintf('LineCount: %04d', $line));
            }

            if (!$prmImport) {
                continue;
            }

            $Cmd = '';
            $i = 0;
            if ($bExist < 0) {
                $i = $disaster->insert(true, false);
                if ($i < 0) {
                    fprintf(STDERR, '%5d %-10s %3d' . "\n", $line, $DisasterSerial, $i);
                }
                $Cmd = 'INSERT';
                continue;
            }
            $i = $disaster->update(true, false);
            $Cmd = 'UPDATE';

            if ($i < 0) {
                fprintf(STDERR, '%5d %-10s %3d' . "\n", $line, $DisasterSerial, $i);
            }
        }
    }

    public function getImportObjectFromArray($values)
    {
        $disaster = new DisasterImport($this->session, '', $this->fields);
        $disaster->importFromArray($values);
        $disasterBeginTime = $this->getDisasterBeginTime($values);
        $serial = $this->getDisasterSerial(substr($disasterBeginTime, 0, 4), $values);
        $disaster->set('DisasterSerial', $serial);

        $disasterId = $disaster->findIdBySerial($disaster->get('DisasterSerial'));
        if (!empty($disasterId)) {
            $disaster->set('DisasterId', $disasterId);
            $disaster->load();
            $disaster->importFromArray($values);
        }
        $disaster->set('DisasterBeginTime', $disasterBeginTime);

        foreach ($this->fixedValues as $fieldName => $value) {
            $disaster->set($fieldName, $value);
        }

        $geographyId = $this->getGeographyId($values);
        $disaster->set('GeographyId', $geographyId);

        $disaster->set('EventId', $this->getEventId($values));
        $disaster->set('CauseId', $this->getCauseId($values));

        // effect fields
        $status = trim($values[5]);
        $statusLowercase = strtolower($status);
        $disaster->set('EffectPeopleDead', in_array($statusLowercase, ['fallecido']) ? 1 : 0);

        $disaster->set('EffectPeopleAffected', 1);

        $isInjured = !in_array($statusLowercase, ['fallecido', 'recuperado']) ? 1 : 0;
        $disaster->set('EffectPeopleInjured', $isInjured);

        $disaster->set('EEF016', in_array($statusLowercase, ['recuperado']) ? 1 : 0);
        $disaster->set('EEF017', in_array($statusLowercase, ['casa']) ? 1 : 0);
        $disaster->set('EEF018', in_array($statusLowercase, ['hospital', 'hospital uci']) ? 1 : 0);

        $disaster->set('EEF019', trim($values[9]));
        $disaster->set('EEF020', $this->getDate($values[11])); // FIS
        $disaster->set('EEF021', $this->getDate($values[14])); // Fecha Recuperación
        $disaster->set('EEF022', $this->getDate($values[13])); // Fecha diagnóstico
        $disaster->set('EEF023', $this->getDate($values[12])); // Fecha muerte
        $disaster->set('EEF024', isset($values[16]) ? $values[16] : ''); // Tipo Recuperación

        // event notes
        $eventNotesList = array_filter(explode(',', trim($disaster->get('EventNotes'))));
        if (!in_array($status, $eventNotesList)) {
            $eventNotesList[] = $status;
        }
        $disaster->set('EventNotes', implode(',', $eventNotesList));

        // extended fields
        $sex = trim($values[7]);
        if ($sex === 'M') {
            $disaster->set('EEF001', 1);
            $disaster->set('EEF002', 0);
        }
        if ($sex === 'F') {
            $disaster->set('EEF001', 0);
            $disaster->set('EEF002', 1);
        }
        $ageRanges = [
            0 => 'EEF003',
            1 => 'EEF004',
            2 => 'EEF005',
            3 => 'EEF006',
            4 => 'EEF007',
            5 => 'EEF008',
            6 => 'EEF009',
            7 => 'EEF010',
            8 => 'EEF011',
            9 => 'EEF013'
        ];
        for ($index = 0; $index < count($ageRanges); $index++) {
            $disaster->set($ageRanges[$index], 0);
        }
        $age = intval($values[6]);
        $ageField = 'EEF013';
        if ($age < 100) {
            $ageIndex = floor($age/10);
            $ageField = $ageRanges[$ageIndex];
        }
        if ($age >= 100) {
            $ageField = 'EEF014';
        }
        $disaster->set($ageField, 1);
        $disaster->set('EEF015', intval($values[6])); // Age

        return $disaster;
    }

    public function loadCSV($ocsv)
    {
        $handle = fopen($ocsv, 'r');
        if (!$handle) {
            return [];
        }
        $res = [];
        while (($data = fgetcsv($handle, 100, ',')) !== false) {
            $row = $data ? $data : [];
            if (count($row) > 0) {
                $res[] = array($row[0], $row[1], $row[2], $row[3], $row[4]);
            }
        }
        fclose($handle);
        return $res;
    }
}
