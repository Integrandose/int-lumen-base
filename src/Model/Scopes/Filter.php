<?php


namespace Int\Lumen\Core\Model\Scopes;

use DB;
use function GuzzleHttp\Psr7\str;

/**
 * Trait Filter
 * @package Int\Lumen\Core\Model\Scopes
 */
trait Filter
{


    /**
     * @param Illuminate\Database\Query\Builder $query
     * @param $attributes
     * @return mixed
     */
    public function scopeFilter($query, $attributes)
    {

        if (!$attributes || $this->filterAttributes == null) {
            return $query;
        }

        foreach ($this->getListFilters($attributes) as $filter) {
            $query = $this->applyQueryFilter($filter, $query);
        }

        return $query;
    }

    /**
     * Get list of the filters
     * @param $attributes
     * @return array
     */
    private function getListFilters($attributes)
    {
        $attributes = $this->getAvaliableFilters($attributes);
        return array_map(array($this, 'parserFilter'), $attributes, array_keys($attributes));
    }

    /**
     * Parser Filter By key
     * @param $value
     * @param $key
     * @return array
     */
    private function parserFilter($value, $key)
    {
        $key = explode('@', $key);
        $operator = $key[1] ?? null;

        return ['attribute' => $key[0], 'operator' => $operator, 'value' => $value];
    }


    /**
     * Get Filters
     * @param $attributes
     * @return array
     */
    private function getAvaliableFilters($attributes)
    {
        return array_filter($attributes, array($this, 'isFilterable'), ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param $key
     * @return bool
     */
    private function isFilterable($key)
    {
        $key = explode('@', $key);
        return in_array($key[0], $this->filterAttributes);
    }


    private function getFilterMethod($filter)
    {
        $filterNamePaths = explode('_', $filter);
        $nameMethod = 'filter' . ucwords(implode('', $filterNamePaths));

        return $nameMethod;
    }

    private function isFilterMethod($method)
    {
        return method_exists($this, $method);
    }


    /**
     * @todo REFATORAR SWITCH
     * @param $filter
     * @param $query
     * @return mixed
     */
    private function applyQueryFilter($filter, $query)
    {

        if ($this->isFilterMethod($this->getFilterMethod($filter['attribute']))) {
            return $this->callMethodFilter($filter, $query);
        }

        switch (strtolower($filter['operator'])) {

            case 'between':
                return $query->whereBetween($filter['attribute'], $this->filterValueToArray($filter['value']));

            case 'notbetween':
                return $query->whereNotBetween($filter['attribute'], $this->filterValueToArray($filter['value']));

            case 'gte':
                return $query->where($filter['attribute'], '>=', $filter['value']);

            case 'gt':
                return $query->where($filter['attribute'], '>', $filter['value']);

            case 'lt':
                return $query->where($filter['attribute'], '<', $filter['value']);

            case 'lte':
                return $query->where($filter['attribute'], '<=', $filter['value']);

            case 'like':
                return $query->where($filter['attribute'], 'like', $filter['value']);

            case 'in':
                return $query->whereIn($filter['attribute'], array_filter(explode(',', $filter['value'])));

            case 'notin':
                return $query->whereNotIn($filter['attribute'], array_filter(explode(',', $filter['value'])));

            case 'null':
                return $query->whereNull($filter['attribute']);

            case 'notnull':
                return $query->whereNotNull($filter['attribute']);

            case 'eq':
            default:
                return $query->where($filter['attribute'], $filter['value']);
        }
    }

    private function filterValueCast($value) {
        return is_numeric($value) ? (float) $value: (string) $value;
    }

    private function filterValueToArray($value)
    {
        $values = array_filter(explode(',', $value));

        return array_map([$this, 'filterValueCast' ], $values);
    }

    /**
     * @param $filter
     * @param $query
     * @return mixed
     */
    private function callMethodFilter($filter, $query)
    {
        return $this->{$this->getFilterMethod($filter['attribute'])}($filter, $query);
    }

}