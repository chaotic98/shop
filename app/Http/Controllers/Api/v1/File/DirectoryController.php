<?php

namespace App\Http\Controllers\Api\v1\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\File\StoreDirectoryRequest;
use App\Models\Directory;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DirectoryController extends Controller
{
    public function index(Request $request,$id)
    {
        $validator = Validator::make($request->all() + [
            'id' => $id,
        ], [
            'id' => [
                'id' => 'exclude_if:id,0'
                    ,'exists:App\Models\Directory,id'
            ]
        ]);
        if ($validator->fails()) {
            $response = [
                'status' => [
                    'code'=>404
                ],
                'error' => $validator->errors()
            ];
        }
        else{
            if($id==0){
                $user = $request->user();
             $file = File::where('directory_id', $user->id)->get();
     
             $dir = Directory::where('user_id', $user->id)->get();
     
             $dir->makeHidden(['parent_id', 'user_id', 'created_at', 'updated_at']);
     
             $response = [
                 'status' => [
                     'code' => 200
                 ],
                 'data' => [
                     'directories' => $dir, 'files' => $file
                 ]
             ]; 
             }
             else{
                 $dir = Directory::where('parent_id', $id)->get();
                 $dir->makeHidden(['parent_id', 'user_id', 'created_at', 'updated_at']);
     
                 $file = File::where('directory_id', $id)->get();
                 $response = [
                     'status' => [
                         'code' => 200,
                     ],
                     'data' =>
                     [
                         'directories' => $dir,
                         'files' => $file
                     ]
                 ];
             }
             
        }
       
        return Controller::setResponse($response);
    }
    
    public function store(StoreDirectoryRequest $request)
    {
        $user = $request->user();
        if ($request->parent_id == 0) {
            Storage::makeDirectory('public/dropBox/'
                . $user->id . '/' . $request->name);

            $dropBox = Directory::create([
                'name' => $request->name,
                'parent_id' => $user->id,
                'user_id' => $user->id,
            ]);

            $response = [
                'status' => [
                    'code' => 201,
                ],
                'data' =>
                [
                    'directory' => $dropBox
                ]
            ];
        } else {
            $dir = Directory::find($request->parent_id);
            Storage::makeDirectory('public/dropBox/'
                . '/'  . $user->id . '/'  . $dir->name . '/' . $request->name);

            $dropBox = Directory::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'user_id' => $user->id,
            ]);

            $response = [
                'status' => [
                    'code' => 201,
                ],
                'data' =>
                [
                    'directory' => $dropBox
                ]
            ];
        }
        return Controller::setResponse($response);
    }

    // public function show(Request $request, $id)
    // {
    //     $validator = Validator::make($request->all() + [
    //         'id' => $id,
    //     ], [

    //         'id' => [
    //             'id' => 'exists:App\Models\File,id'
    //         ]
    //     ]);
    //     if ($validator->passes()) {
    //         $dir = Directory::where('parent_id', $id)->get();
    //         $dir->makeHidden(['parent_id', 'user_id', 'created_at', 'updated_at']);

    //         $file = File::where('directory_id', $id)->get();
    //         $response = [
    //             'status' => [
    //                 'code' => 200,
    //             ],
    //             'data' =>
    //             [
    //                 'directories' => $dir,
    //                 'files' => $file
    //             ]
    //         ];
    //     } else {
    //         $response = [
    //             'status' => [
    //                 'code' => 404,
    //             ],
    //             'error' => $validator->errors()
    //         ];
    //         return Controller::setResponse($response);
    //     }
    //     return Controller::setResponse($response);
    // }
}
