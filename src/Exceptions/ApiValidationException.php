<?php

namespace Int\Lumen\Core\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ApiValidationException extends ValidationException
{

    /**
     * Create a new exception instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public function __construct($validator, $response = null)
    {
        parent::__construct('The given data failed to pass validation.');

        $data = [
            "error" => [
                "message" => $this->getMessage(),
                "erros" => $validator->getMessageBag()->toArray()
            ]
        ];

        Log::warning($this->getMessage(), $data);

        $this->response = $response->setContent(json_encode($data));
        $this->validator = $validator;
    }



}
