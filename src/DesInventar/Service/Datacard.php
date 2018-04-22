<?php

namespace DesInventar\Service;

use DesInventar\Common\Util;

class Datacard extends Service
{
    const CMD_NEW = 1;

    protected function nextSerialSuffix($year, $prefix, $separator, $length)
    {
        $query = $this->factory->newSelect();
        $query->from('Disaster')
            ->cols(['DisasterSerial'])
            ->where('(DisasterBeginTime LIKE :year)')
            ->where('(DisasterSerial REGEXP :prefix)')
            ->orderBy(['DisasterSerial DESC'])
            ->limit(100)
            ->bindValues([
                'year' => $year . '%',
                'prefix' => '/^' . $prefix . addSlashes($separator) . '[0-9]{' . $length . '}.*$/',
            ]);
        $serial = null;
        $sth = $this->pdo->perform($query->getStatement(), $query->getBindValues());
        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $serial = (int) substr($row['DisasterSerial'], strlen($prefix) + strlen($separator));
            if (! empty($serial)) {
                break;
            }
        }
        if (empty($serial)) {
            $serial = 0;
        }
        $serial = $serial + 1;
        return $serial;
    }

    public function nextSerial($year, $prefix, $length, $separator)
    {
        $serial = sprintf('%0' . (int)$length . 'd', $this->nextSerialSuffix($year, $prefix, $separator, $length));
        return $prefix . $separator . $serial;
    }

    // Convert Post Form to DesInventar Disaster Table struct
    // Insert  (1) create DisasterId.
    // Update  (2) keep RecordCreation and RecordAuthor
    public function form2disaster($form, $icmd)
    {
        $util = new Util();

        $data = [];
        foreach ($form as $key => $value) {
            $newValue = $util->removeSpecialChars($value);
            $data[$key] = $newValue;
        }

        // On Update
        $data['DisasterId'] = $form['DisasterId'];
        $data['RecordUpdate'] = gmdate('c');
        if ($icmd == self::CMD_NEW) {
            // New Disaster
            if ($data['DisasterId'] == '') {
                $data['DisasterId'] = $util->uuid4();
            }
            $data['RecordCreation'] = $data['RecordUpdate'];
        }

        // Disaster date
        $str = sprintf('%04d', $form['DisasterBeginTime'][0]);
        if (!empty($form['DisasterBeginTime'][1])) {
            $str .= '-' . sprintf('%02d', $form['DisasterBeginTime'][1]);
            if (!empty($form['DisasterBeginTime'][2])) {
                $str .= '-' . sprintf('%02d', $form['DisasterBeginTime'][2]);
            }
        }
        $data['DisasterBeginTime'] = $str;
        return $data;
    }
}
