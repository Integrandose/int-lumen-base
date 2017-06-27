<?php

namespace Int\Lumen\Core\Http\Controllers;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Int\Lumen\Core\Exceptions\ApiValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as CollectionFractal;
use League\Fractal\Resource\Item as ItemFractal;
use League\Fractal\TransformerAbstract;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\JsonGuard\Validator;

class ApiController extends BaseController
{


    protected $status = Response::HTTP_OK;

    protected $transformer;

    protected $maxLimit = 100;

    protected $defaultLimit = 20;


    public function __construct(Manager $manager, Request $request)
    {

        if ($request->has('include')) {
            $manager->parseIncludes($request->get('include'));
        }
        $this->transformer = $manager;
    }

    protected function getLanguage() {
        return  app('request')->get('language');
    }

    protected function getSort()
    {
        $request = app('request');
        return $request->get('sort') ?? null;
    }

    protected function getFilter()
    {
        $request = app('request');
        return $request->get('filter');
    }

    protected function getLimit(): int
    {

        $request = app('request');

        $limit = $request->get('limit') ?? $this->defaultLimit;
        return (int)$limit > $this->maxLimit ? $this->maxLimit : $limit;
    }

    /**
     * @param $data  Collection|Model
     * @param TransformerAbstract $transform
     * @return array
     */
    public function transformData($data, TransformerAbstract $transform)
    {

        if ($data instanceof LengthAwarePaginator || $data instanceof Collection) return $this->transformCollection($data, $transform);
        return $this->transformItem($data, $transform);
    }

    /**
     * @param $data
     * @param TransformerAbstract $transform
     * @return array
     */
    private function transformItem($data, TransformerAbstract $transform)
    {
        $resource = new ItemFractal($data, $transform);
        return $this->transformer->createData($resource)->toArray();
    }

    /**
     * @param $data
     * @param TransformerAbstract $transform
     * @return array
     */
    private function transformCollection($data, TransformerAbstract $transform)
    {

        if ($data instanceof Collection) {
            $resource = new CollectionFractal($data, $transform);
        }

        if ($data instanceof LengthAwarePaginator) {
            $resource = new CollectionFractal($data->getCollection(), $transform);
            $resource->setPaginator(new IlluminatePaginatorAdapter($data));
        }

        return $this->transformer->createData($resource)->toArray();

    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithSuccess($data, $headers = [])
    {
        return $this->setStatus(Response::HTTP_OK)->respond($data, $headers);
    }

    /**
     * @param $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondCreated($data, $headers = [])
    {
        return $this->setStatus(Response::HTTP_CREATED)->respond($data, $headers);
    }


    /**
     * @param null $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondNotFound($data = null, $headers = [])
    {
        return $this->setStatus(Response::HTTP_NOT_FOUND)->respondWithError($data, $headers);
    }

    /**
     * @param null $message
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($message = null, $errors = [], $headers = [])
    {

        $message = $message ?? Response::$statusTexts[$this->getStatus()];

        $data = [
            "message" => $message,
        ];


        Log::error($message, $data);

        return $this->respond($data, $headers);
    }

    /**
     * @param $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatus(), $headers);
    }

    /**
     * Throw the failed validation exception.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function throwValidationException(Request $request, $validator)
    {
        throw new ApiValidationException($validator, $this->buildFailedValidationResponse(
            $request, $this->formatValidationErrors($validator)
        ));
    }


}
