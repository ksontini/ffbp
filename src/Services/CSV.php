<?php

namespace Services;
use League\Csv\Writer;
use SplTempFileObject;

class CSV extends BaseService
{
    public function createDocument($object, $config, $fields)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $csv->insertOne($config);

        foreach ($object as $element) {
            $obj = array();

            foreach ($fields as $field) {
                if (is_object($element))
                    $obj[] = $element->{$field};
                else
                    $obj[] = $element[$field];
            }

            $csv->insertOne($obj);
        }

        $csv->output('export.csv');
        return true;
    }
}