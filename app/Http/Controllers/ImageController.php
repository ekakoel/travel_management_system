<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image; // Import the Intervention Image facade
use App\Models\Product;

class ImageController extends Controller
{
    public function showResizedImage($id, $filename)
    {
        $product = Product::findOrFail($id);
        $path = public_path("images/products/{$id}/" . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        // Resize the image
        $image = Image::make($path)->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
        });

        return $image->response('jpg');
    }
}
