<?php

namespace hoo\io\http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InnerResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'code'          =>  $this->resource['code'] ?? 400,
            'message'       =>  $this->resource['message'] ?? '',
            'data'          =>  $this->resource['data'] ?? []
        ];
    }
}
