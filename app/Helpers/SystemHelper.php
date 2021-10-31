<?php


if ( ! function_exists('get_storage_file_url') )
{
    function get_storage_file_url($path = null, $size = 'small')
    {
        if ( !$path )
            return get_placeholder_img($size);

        // return asset("image/{$path}?p={$size}");
        return url("image/{$path}?p={$size}");
    }
}

if ( ! function_exists('get_placeholder_img') )
{
    function get_placeholder_img($size = 'small')
    {
        $size = config("image.sizes.{$size}");

        if ($size && is_array($size))
            return "https://placehold.it/{$size['w']}x{$size['h']}/eee?text=" . trans('app.no_img_available');

        return url("images/placeholders/no_img.png");
    }
}


if ( ! function_exists('image_cache_path') )
{
    function image_cache_path($path = Null)
    {
        $path = config('image.cache_dir') . '/' . $path;
        return Str::finish($path, '/');
    }
}
