<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageValidation
{
    public function validateImage(Request $req, $imageForm, $path)
    {
        $req->validate([$imageForm => 'mimes:jpg,jpeg,png']);
        $file = $req->file($imageForm);
        $filename = Str::random(8);
        $extension = $file->getClientOriginalExtension();
        $fileName = $filename . '-' . Carbon::now('Asia/Jayapura')->format('Y-m-d') . '.' . $extension;
        $file_path = $path . $fileName;

        $insertFile = Storage::disk('public')->put($file_path, file_get_contents($file));

        if ($insertFile) {
            $linkFile = Storage::url($file_path);
            return $linkFile;
        }
    }
}