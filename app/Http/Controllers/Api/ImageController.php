<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ImageStoreRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Symfony\Component\HttpFoundation\Response;
use File;

class ImageController extends Controller
{
    
    public function index()
    {
        $image = Image::all();
        return ImageResource::collection($image);
    }

    public function store(ImageStoreRequest $request)
    {
        $validatedData = $request->validated();
        if ($file = $request->file('image')) {
            $extension = $file->extension();
            $destinationPath = public_path() . '/uploads/images/';
            $safeName = str_random(10) . '.' . $extension;
            $file->move($destinationPath, $safeName);
            $validatedData['image'] = $safeName;
        }
        $data = Image::create($validatedData);

        return new ImageResource($image);
    }

    public function update(ImageStoreRequest $request, Image $image)
    {
        $data =  Image::findOrfail($request->id);
        $validatedData = $request->validated();
        if ($file = $request->file('image')) {
            $extension = $file->extension();
            $destinationPath = public_path() . '/uploads/images/';
            $safeName = str_random(10) . '.' . $extension;
            $file->move($destinationPath, $safeName);
            //delete old pic if exists
            if (File::exists($destinationPath . $data->image)) {
                File::delete($destinationPath . $data->image);
            }
            //save new file path into db
            $validatedData['image'] = $safeName;
        }
        $data = Image::update($validatedData);

        return new ImageResource($image);
    }

    public function destroy(Image $image)
    {
        $image->delete();

        return response(null, 204);
    }

    public function show(ImageStoreRequest $request)
    {
        $data = Image::findOrfail($request->id);

        return response($data, 200);
    }
}
