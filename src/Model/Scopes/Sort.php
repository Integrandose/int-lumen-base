<?php


namespace Int\Lumen\Core\Model\Scopes;


/**
 * Trait Sort
 * @package Int\Lumen\Core\Model\Scopes
 */
trait Sort
{

    public function scopeSort($query, $attributes)
    {

        if (!$attributes || $this->sortAttributes == null) {
            return $query;
        }

        foreach ($this->getListSorts($attributes) as $sort) {

            $query = $this->applyQuerySort($sort, $query);
        }


        return $query;
    }

    private function getListSorts($attributes)
    {
        $attributesAvaliable = $this->getAvaliableSorts(explode(',', $attributes));
        return array_map(array($this, 'parserSort'), $attributesAvaliable);
    }


    private function getAvaliableSorts($attributes)
    {
        return array_filter($attributes, array($this, 'isSortable'));
    }

    /**
     * @param $value
     * @return bool
     */
    private function isSortable($value)
    {
        return in_array(str_replace('-', '', $value), $this->sortAttributes);
    }


    private function applyQuerySort($sort, $query)
    {

        if ($this->isSortMethod($this->getSortMethod($sort['attribute']))) {
            return $this->callSortMethod($sort, $query);
        }

        $query->orderBy($sort['attribute'], $sort['direction']);


        return $query;
    }

    private function parserSort($value)
    {
        $hasDirection = (substr($value, 0, 1) === "-");

        return $sort = [
            'direction' => $hasDirection ? 'DESC' : 'ASC',
            'attribute' => $hasDirection ? substr($value, 1) : $value
        ];
    }

    private function callSortMethod($filter, $query)
    {
        return $this->{$this->getSortMethod($filter['attribute'])}($filter, $query);
    }

    private function getSortMethod($filter)
    {
        $filterNamePaths = explode('_', $filter);
        $nameMethod = 'sort' . ucwords(implode('', $filterNamePaths));

        return $nameMethod;
    }

    private function isSortMethod($method)
    {
        return method_exists($this, $method);
    }

}