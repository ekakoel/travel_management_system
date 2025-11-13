<?php

namespace App\Http\Controllers;

use App\Models\ToursImages;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreToursImagesRequest;
use App\Http\Requests\UpdateToursImagesRequest;

class ToursImagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreToursImagesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'tour_id' => 'required|integer|exists:tours,id',
        ]);

        // Ambil file yang diupload
        $file = $request->file('file');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        // Simpan file ke storage
        $filePath = $file->move('tours/tours-galleries', $filename, 'public');

        // Simpan ke database
        $image = ToursImages::create([
            'tour_id' => $request->tour_id,
            'image' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'image_id' => $image->id,
            'path' => asset($filePath),
        ]);
    }

    public function upload(Request $request)
    {
        try {
            // validasi file
            $request->validate([
                'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
                'tour_id' => 'required|integer|exists:tours,id',
            ]);

            // simpan file ke storage
            // $path = $request->file('file')->store('/uploads/tours/gallery', 'public');
            
            $file = $request->file('file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            // $filePath = $file->move('tours/tour-gallery', $filename, 'public');
            $path = $file->storeAs('tours/tour-gallery', $filename, 'public');
            $validated['file'] = $path;
            

            // simpan data ke database
            $image = ToursImages::create([
                'tour_id' => $request->tour_id,
                'image' => $filename,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully!',
                'image' => $image
            ], 200);

        } catch (\Exception $e) {
            // debug error ke console browser
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ToursImages  $toursImages
     * @return \Illuminate\Http\Response
     */
    public function show(ToursImages $toursImages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ToursImages  $toursImages
     * @return \Illuminate\Http\Response
     */
    public function edit(ToursImages $toursImages)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {
            $tour_images = ToursImages::findOrFail($id);

            if ($request->hasFile('file')) {
                // Hapus file lama jika ada
                if ($tour_images->image && Storage::disk('public')->exists('tours/tour-gallery/' . $tour_images->image)) {
                    Storage::disk('public')->delete('tours/tour-gallery/' . $tour_images->image);
                }

                // Upload file baru ke storage/app/public/tours/tour-gallery
                $file = $request->file('file');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                            . '.' . $file->getClientOriginalExtension();

                $file->storeAs('tours/tour-gallery', $filename);

                // Simpan ke database hanya nama filenya
                $tour_images->image = $filename;
                $tour_images->save();
            }

            return response()->json([
                'success' => true,
                // Akses URL publik melalui 'storage/...'
                'url' => asset('storage/tours/tour-gallery/' . $tour_images->image)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tour_image = ToursImages::findOrFail($id);
            if ($tour_image->image && Storage::disk('public')->exists('tours/tour-gallery/' . $tour_image->image)) {
                Storage::disk('public')->delete('tours/tour-gallery/' . $tour_image->image);
            }

            $tour_image->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
