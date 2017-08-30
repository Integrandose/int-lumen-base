<?php

namespace Int\Lumen\Core\Transformer;

use League\Fractal\Serializer\DataArraySerializer as Serializer;
use League\Fractal\Pagination\PaginatorInterface;


class DataArraySerializer extends Serializer {


    /**
     * Serialize the paginator.
     *
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $currentPage = (int) $paginator->getCurrentPage();
        $lastPage = (int) $paginator->getLastPage();

        $pagination = [
            'total' => (int) $paginator->getTotal(),
            'count' => (int) $paginator->getCount(),
            'per_page' => (int) $paginator->getPerPage(),
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'total_pages' => $lastPage
        ];

        if ($currentPage > 1) {
            $pagination['prev_page_url'] = $paginator->getUrl($currentPage - 1);
        }

        if ($currentPage < $lastPage) {
            $pagination['next_page_url'] = $paginator->getUrl($currentPage + 1);
        }

        return ['pagination' => $pagination];
    }
}