<?php


namespace Int\Lumen\Core\Model\Scopes;

use DB;
use function GuzzleHttp\Psr7\str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Traits Filter
 * @package Int\Lumen\Core\Model\Scopes
 */
trait Filter
{


    /**
     * @param Illuminate\Database\Query\Builder $query
     * @param $attributes
     * @return mixed
     */
    public function scopeFilter($query, $attributes, $language = null)
    {
        $this->language = $language;

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
        $attributesAvaliable = $this->getAvaliableFilters($attributes);
        return array_map(array($this, 'parserFilter'), $attributesAvaliable, array_keys($attributesAvaliable));
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
        if (in_array($key[0], $this->filterAttributes)) {
            return true;
        }

        throw new BadRequestHttpException('invalid attribute for filter: ' . $key[0]);

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
     * @todo implementar filtro com language=all
     * @param $filter
     * @param $query
     * @return mixed
     */
    private function applyQueryFilter($filter, $query)
    {

        if (empty($filter['value'])) {
            return $query;
        }

        if ($this->isFilterMethod($this->getFilterMethod($filter['attribute']))) {
            return $this->callMethodFilter($filter, $query);
        }


        if (!is_null($this->language) && $this->language != 'all' && in_array($filter['attribute'], $this->translationAttributes)) {
            $filter['attribute'] .= '.' . $this->language;
        }


        switch (strtolower($filter['operator'])) {

            case 'between':
                return $query->whereBetween($filter['attribute'], $this->filterValueToArray($filter['value']));

            case 'notbetween':
                return $query->whereNotBetween($filter['attribute'], $this->filterValueToArray($filter['value']));

            case 'gte':
                return $query->where($filter['attribute'], '>=', $this->filterValueCast($filter['value']));

            case 'gt':
                return $query->where($filter['attribute'], '>', $this->filterValueCast($filter['value']));

            case 'lt':
                return $query->where($filter['attribute'], '<', $this->filterValueCast($filter['value']));

            case 'lte':
                return $query->where($filter['attribute'], '<=', $this->filterValueCast($filter['value']));

            case 'like':
                return $query->where($filter['attribute'], 'like', $filter['value']);

            case 'in':

                $values = explode(',', $filter['value']);
                $valuesJson = json_encode($values);
                return ($this->isJsonColumn($filter['attribute']))
                    ? $query->whereRaw("JSON_CONTAINS({$filter['attribute']}, '{$valuesJson}')"): $query->whereIn($filter['attribute'], array_filter(explode(',', $filter['value'])));

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

    private function filterValueCast($value)
    {
        switch ($value) {
            case is_numeric($value):
                return (float) $value;
                break;

            case is_array($value):
                return (isset($value['date']) && isset($value['timezone_type']) && isset($value['timezone'])) ? new \DateTime($value['date']) : null;
                break;

            default:
                return (string) $value;
                break;
        }
    }

    private function filterValueToArray($value)
    {
        $values = array_filter(explode(',', $value));

        return array_map([$this, 'filterValueCast'], $values);
    }

    private function isJsonColumn($column)
    {
        return $this->hasCast($column, 'json');
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