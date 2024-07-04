<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenericResponseResource extends JsonResource
{
    protected $statusCode;
    protected $message;

    function __construct($resource, $statusCode = 200, $message = "")
    {
        parent::__construct($resource);
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->statusCode);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "status" => $this->statusCode,
            "message"=> $this->message,
            "data" => $this->resource,
        ];
    }
}
