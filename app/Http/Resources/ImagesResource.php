<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\SystemHelper;
class ImagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url' => get_storage_file_url($this->path,'full'),
            'image_url_1' => get_storage_file_url($this->path,'full'),
            'image_url_2' => get_storage_file_url($this->path,'2x'),
            'image_url_3' => get_storage_file_url($this->path,'3x'),
            'image_url_4' => get_storage_file_url($this->path,'4x'),
            'width_1' => '1280',
            'width_2' => '2013',
            'width_3' => '3019',
            'width_4' => '4025',
            'name' => $this->name,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
