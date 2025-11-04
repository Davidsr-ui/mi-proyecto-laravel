<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Http\Requests\MovieRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::latest()->paginate(12);
        return view('movies.index', compact('movies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('movies', 'public');
            }

            Movie::create($data);

            return redirect()->route('movies.index')
                ->with('success', '¡Película creada exitosamente!');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')
                ->with('error', 'Error al crear la película: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        return response()->json($movie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, Movie $movie)
    {
        try {
            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($movie->image && Storage::exists($movie->image)) {
                    Storage::delete($movie->image);
                }
                $data['image'] = $request->file('image')->store('movies', 'public');
            }

            $movie->update($data);

            return redirect()->route('movies.index')
                ->with('success', '¡Película actualizada exitosamente!');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')
                ->with('error', 'Error al actualizar la película: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        try {
            // Delete image if exists
            if ($movie->image && Storage::exists($movie->image)) {
                Storage::delete($movie->image);
            }

            $movie->delete();

            return redirect()->route('movies.index')
                ->with('success', '¡Película eliminada exitosamente!');
        } catch (\Exception $e) {
            return redirect()->route('movies.index')
                ->with('error', 'Error al eliminar la película: ' . $e->getMessage());
        }
    }
}