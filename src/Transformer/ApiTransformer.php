<?php

namespace Int\Lumen\Core\Transformer;


use League\Fractal\TransformerAbstract;

class ApiTransformer extends TransformerAbstract
{

    protected function filterFields($fields, $type) {

        $request = app('request');
        $filterFields = $request->get('fields');

        if (!isset($filterFields[$type]) ) {
            return $fields;
        }

        $listFields = explode(',', $filterFields[$type]);

        return collect($fields)->only($listFields)->toArray();
    }

    protected function getLanguage() {
        return  app('request')->get('language');
    }

}