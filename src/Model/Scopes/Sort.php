<?php


namespace Int\Lumen\Core\Model\Scopes;


/**
 * Trait Sort
 * @package Int\Lumen\Core\Model\Scopes
 */
trait Sort
{

    /**
     *  Add OrderBy on Query
     * @param string $fields
     * @return Illuminate\Database\Query\Builder
     */
    public function scopeSort($query, $fields)
    {

        if (!$fields) {
            return $query;
        }

        foreach ($this->convertSorts($fields) as $sort) {
            $query->orderBy($sort['field'], $sort['direction']);
        }

        return $query;
    }

    /**
     * Convert to array with infos to the orderBy
     * @param string $fields
     * @return array
     */
    private function convertSorts($fields)
    {
        $fields = explode(',', $fields);
        $fields = array_filter($fields);

        return array_map(function ($value) {
            $hasDirection = (substr($value, 0, 1) === "-");

            $sort = [
                'direction' => $hasDirection ? 'DESC' : 'ASC',
                'field' => $hasDirection ? substr($value, 1) : $value
            ];

            return $sort;
        }, $fields);
    }
}