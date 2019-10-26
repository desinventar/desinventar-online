<?php

namespace DesInventar\Helpers;

class Dictionary
{
    public function findById($dictionary, $id)
    {
        $filteredArray = array_filter($dictionary, function ($value) use ($id) {
            return $value['id'] == $id;
        });
        if (!$filteredArray) {
            return false;
        }
        return reset($filteredArray);
    }
}
