<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BookController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index()
    {
        $books = Book::with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::all();

        return Inertia::render('Books/Index', [
            'books' => $books,
            'categories' => $categories,
            'flash' => [
                'success' => session('success')
            ]
        ]);
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Books/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'editorial' => 'required|string|max:255',
            'edicion' => 'required|string|max:255',
            'fecha_lanzamiento' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'imagen_portada' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ], [
            'nombre.required' => 'El título del libro es obligatorio.',
            'autor.required' => 'El autor del libro es obligatorio.',
            'editorial.required' => 'La editorial es obligatoria.',
            'edicion.required' => 'La edición es obligatoria.',
            'fecha_lanzamiento.required' => 'La fecha de lanzamiento es obligatoria.',
            'fecha_lanzamiento.date' => 'Ingrese una fecha válida.',
            'category_id.required' => 'Debe seleccionar una categoría.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
            'imagen_portada.required' => 'La imagen de portada es obligatoria.',
            'imagen_portada.image' => 'El archivo debe ser una imagen.',
            'imagen_portada.mimes' => 'Formatos de imagen válidos: jpeg, png, jpg, webp, gif.',
            'imagen_portada.max' => 'La imagen no debe pesar más de 2MB.',
        ]);

        if ($request->hasFile('imagen_portada')) {
            $path = $request->file('imagen_portada')->store('covers', 'public');
            $validated['imagen_portada'] = Storage::url($path);
        }

        Book::create($validated);

        return redirect()->route('books.index')->with('success', '¡El libro ha sido registrado exitosamente!');
    }
}
