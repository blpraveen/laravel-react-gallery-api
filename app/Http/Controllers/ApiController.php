<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use App\Models\Image;
use App\Repositories\ImageRepository;
use App\Http\Resources\ImagesResource;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use League\Glide\Server;

class ApiController extends Controller
{
     /**
     * @var CartRepository
     */
    protected $imageRepository;

     /**
     * ApiController constructor.
     *
     * @param ImageRepository $imageRepository
     */
    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function register(Request $request)
    {
    	//Validate data
        $data = $request->only('name', 'email', 'password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $user = User::create([
        	'name' => $request->name,
        	'email' => $request->email,
        	'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }
 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                	'success' => false,
                	'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
    	return $credentials;
            return response()->json([
                	'success' => false,
                	'message' => 'Could not create token.',
                ], 500);
        }
 	
 		//Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
 
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

		//Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);
 
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
 
    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
 
        $user = JWTAuth::authenticate($request->token);
 
        return response()->json(['user' => $user]);
    }

    public function upload(Request $request){
        try {
            if ($request->hasFile('image')) {
                $this->imageRepository->uploadImage($request->file('image'));
            }
             return response()->json([
                'success' => true,
                'message' => 'Image uploaded'
            ]);
         } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, failed to upload'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function loadImage(){
         try {
            $images = Image::paginate(20);
             return response()->json([
                'success' => true,
                'message' => 'Image uploaded',
                'images' => ImagesResource::collection($images)
            ]);
         } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, failed to upload'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

     public function show(Request $request, Server $server, $path)
    {
        $this->setConfigs($request);

        return $server->getImageResponse($path, $request->all());
    }

    /**
     * Set Config settings for the image manupulation
     *
     * @param Request $request [description]
     */
    private function setConfigs(Request $request)
    {
        if (config('image.background_color'))
            $request->merge(['bg' => config('image.background_color')]);

        return $request;
    }
}