<?php


namespace Int\Lumen\Core\Model\Scopes;
use DB;

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
            $query->where($filter['attribute'], $filter['operator'], $filter['value']);
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

        return ['attribute' => $key[0], 'operator' => $this->parserOperator($operator), 'value' => $value];
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

    /**
     * Parser Operator
     * @param $key
     * @return string
     */
    private function parserOperator($key)
    {

        switch (strtolower($key)) {

            case 'gte':
                return '>=';

            case 'gt':
                return '>';

            case 'lt':
                return '<';

            case 'lte':
                return '<=';

            case 'like':
                return 'LIKE';

            case 'in':
                return 'IN';

            case 'eq':
            default:
                return '=';
        }
    }

}