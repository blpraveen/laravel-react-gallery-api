<?php

namespace App\Repositories;

use App\Exceptions\GeneralException;
use App\Models\Image;
use App\Repositories\BaseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
/**
 * Class ImageRepository.
 */
class ImageRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Image::class;

    protected $upload_path;

     /**
     * Storage Class Object.
     *
     * @var \Illuminate\Support\Facades\Storage
     */
    protected $storage;

    public function __construct()
    {
        $this->upload_path = 'images';
        $this->storage = Storage::disk('public');
    }

    /**
     * @param      $input
     * @param bool $expired
     *
     * @throws GeneralException
     * @return bool
     */
    public function uploadImage($image)
    {
        $path = Storage::put($this->upload_path, $image);

        $imagesize = getimagesize($_FILES['image']['tmp_name']);

        return $this->createImage($path, $image->getClientOriginalName(), $image->getClientOriginalExtension(), $image->getSize(),$imagesize[0],$imagesize[1]);
       
    }

    /**
     * Create image model
     *
     * @return array
     */
    private function createImage($path, $name, $ext = '.jpeg', $size = Null,$width=Null,$height = Null)
    {
        return Image::create([
            'path' => $path,
            'name' => $name,
            'extension' => $ext,
            'size' => $size,
            'width' => $width,
            'height' => $height,
        ]);
    }
     

    
}
