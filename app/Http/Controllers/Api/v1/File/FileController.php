<?php

namespace App\Http\Controllers\Api\v1\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\File\DownloadFileRequest;
use App\Http\Requests\Api\v1\File\StoreFileRequest;
use App\Models\Directory;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function store(StoreFileRequest $request)
    {
        $user = $request->user();
        
        if ($request->directory_id == 0) {

            $newFileName = time() . '.' . $request->file->getClientOriginalExtension();
            $filePath = Storage::putFileAs(
                'public/dropBox' . '/' . $user->id,
                $request->file,
                $newFileName
            );

            $dropBox = File::create([
                'name' => $newFileName,
                'directory_id' => $user->id,
                'user_id' => $user->id,
                'file_path' => $filePath,
            ]);

            $response = [
                'status' => [
                    'code' => 201,
                ],
                'data' =>
                [
                    'drop_box' => $dropBox
                ]

            ];
        } else {
            $dir = Directory::find($request->directory_id);
            if ($dir->parent_id == $user->id) {
                $newFileName = time() . '.' . $request->file->getClientOriginalExtension();
                $filePath = Storage::putFileAs(
                    'public/dropBox' . '/' . $user->id
                        . '/' . $dir->name,
                    $request->file,
                    $newFileName
                );

                $dropBox = File::create([
                    'name' => $newFileName,
                    'directory_id' => $dir->id,
                    'user_id' => $user->id,
                    'file_path' => $filePath,
                ]);

                $response = [
                    'status' => [
                        'code' => 201,
                    ],
                    'data' =>
                    [
                        'drop_box' => $dropBox
                    ]

                ];
            } else {
                $parentDir = Directory::find($dir->parent_id);
                $newFileName = time() . '.' . $request->file->getClientOriginalExtension();
                $filePath = Storage::putFileAs(
                    'public/dropBox' . '/' . $user->id
                        . '/' . $parentDir->name . '/' . $dir->name,
                    $request->file,
                    $newFileName
                );

                $dropBox = File::create([
                    'name' => $request->name,
                    'directory_id' => $dir->id,
                    'user_id' => $user->id,
                    'file_path' => $filePath,
                ]);

                $response = [
                    'status' => [
                        'code' => 201,
                    ],
                    'data' =>
                    [
                        'drop_box' => $dropBox
                    ]

                ];
            }
        }

        return Controller::setResponse($response);
    }

    public function download(Request $request, $id)
    {
        $file = File::find($id);
        $validator = Validator::make($request->all() + [
            'id' => $id,
        ], [

            'id' => [
                'id' => 'exists:App\Models\File,id'
            ]
        ]);
        if ($validator->passes()) {
            // $filePath = storage_path('app/public/dropBox/' . '/'
            //     . $user->id . $request->name);
            $filePath = storage_path('app' . '/' . $file->file_path);

            $headers = ['Content-Type: application/pdf'];

            $fileName = time() . '.pdf';

            return response()->download($filePath, $fileName, $headers);
        }
        else{
            $response = [
                'status' => [
                    'code' => 404,
                ],
                'error' => $validator->errors()
            ];
            return Controller::setResponse($response);

        }
        
    }
}
