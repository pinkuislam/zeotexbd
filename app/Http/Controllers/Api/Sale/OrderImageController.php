<?php

namespace App\Http\Controllers\Api\Sale;

use App\Http\Controllers\Controller;
use App\Models\OrderImage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Sudip\MediaUploader\Facades\MediaUploader;

class OrderImageController extends Controller
{
    public function store($orderId, Request $request)
    {
        // Body Parameters
        $validator = Validator::make($request->all(), [
            'file' => 'required|array',
            'file.*' => 'required|image',
        ]);

        if ($validator->fails()) {
            return validatorErrorResponse($validator);
        }
        $validatedData = $validator->valid();

        try {
            if ($request->hasFile('file')) {
                $files = $validatedData['file'];
                foreach ($files as $file) {
                    $fileUpload = MediaUploader::imageUpload($file, OrderImage::ORDER_IMAGE_PATH, 1, null, [600, 600], [80, 80]);
                    OrderImage::create([
                        'order_id' => $orderId,
                        'image' => $fileUpload['name'],
                        'image_url' => $fileUpload['url'],
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Collage uploaded successfully.',
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'No images uploaded!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($orderId, $id)
    {
        $data = OrderImage::findOrFail($id);

        // Delete Current File
        MediaUploader::delete(OrderImage::ORDER_IMAGE_PATH, $data->image, true);

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collage deleted successfully',
        ]);
    }
}
