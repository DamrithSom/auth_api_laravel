<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ErlandMuchasa\LaravelFileUploader\FileUploader;
use Illuminate\Support\Facades\Log;
class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::all();
        return response()->json(
            [
                'status' => '200 OK',
                'message' => 'success',
                'data' => Book::all(),
            ],200
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
{
    // Validate the incoming request
    $validatedData = $request->validate([
        'title' => 'required',
        'author' => 'required',
        'description' => 'required',
        'price' => 'required',
        'partImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
    ]);

    try {
        // Handle the image upload if provided
        if ($request->hasFile('partImage')) {
            $file = $request->file('partImage');
            
            // Store the file in the 'images' folder in the 'public' disk
            $path = $file->store('images', 'public'); 
            
            // Generate the public URL for the image
            $imageUrl = asset('storage/' . $path); 
        } else {
            $imageUrl = null; // If no image is provided, set the URL as null
        }

        // Create the book record using mass assignment
        $book = Book::create([
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'partImage' => $imageUrl, // Use the generated image URL or null
        ]);

        return response()->json([
            'message' => 'Product created successfully!',
            'data' => $book
        ], 201); // HTTP 201: Created
    } catch (\Exception $e) {
        // Handle any errors that might occur during the save process
        return response()->json([
            'error' => 'Something went wrong while creating the book.',
            'details' => $e->getMessage() // Optional: provide more detailed error message
        ], 500); // HTTP 500: Internal Server Error
    }
}

    

    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = Book::find($id);
        if($book)
        {
            return response()->json([
                'status' => '200 OK',
                'message' => 'success',
                'data' => $book,
            ],200);
        }
        else{
            return response()->json([
                'status' => '404 Not Found',
                'message' => 'Book not found',
            ],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $book = Book::find($id);
    
    if ($book) {
        // Check if the book has an associated image and delete it from storage
        if ($book->partImage) {
            // Extract the filename from the URL stored in the database (e.g., /storage/images/filename.jpg)
            $imagePath = str_replace('/storage/', 'public/', $book->partImage);
            
            // Check if the file exists in storage and delete it
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
                Log::info("Deleted image: " . $imagePath); // Log the file path
            } else {
                Log::warning("Image not found: " . $imagePath); // Log if file was not found
            }
        }

        // Delete the book from the database
        $book->delete();

        return response()->json([
            'status' => '200 OK',
            'message' => 'Book deleted successfully',
        ], 200);
    } else {
        // Log the case where the book wasn't found
        Log::warning("Book not found: " . $id);

        return response()->json([
            'status' => '404 Not Found',
            'message' => 'Book not found',
        ], 404);
    }
}

    
    
}
