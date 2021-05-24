<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $xRequestId = $request->header('X-Request-Id');
        $xTimestamp = $request->header('X-Timestamp');
        $xSignature = $request->header('X-Signature');
        //$data = json_encode($request->json()->all(), true);
        $data = $request->json()->all();
        $contents = print_r($data, 1);
        Storage::append('log.txt', $contents);
        //print_r($data['customer']);
        return response()->json($data, 201);
    }
}
