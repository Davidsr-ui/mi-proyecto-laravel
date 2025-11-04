<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catálogo de Películas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 0;
        }
        .movie-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border-radius: 15px;
            overflow: hidden;
        }
        .movie-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }
        .movie-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .rating-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: #ffc107;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }
        .container-custom {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-4 fw-bold text-primary">
                    <i class="bi bi-film"></i> Catálogo de Películas
                </h1>
                <button class="btn btn-custom btn-lg" data-bs-toggle="modal" data-bs-target="#createMovieModal">
                    <i class="bi bi-plus-circle"></i> Nueva Película
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                @forelse($movies as $movie)
                    <div class="col-md-4 col-lg-3">
                        <div class="card movie-card shadow">
                            <div class="position-relative">
                                <img src="{{ $movie->image ? asset('storage/' . $movie->image) : 'https://via.placeholder.com/300x400?text=Sin+Imagen' }}" 
                                     class="card-img-top movie-image" 
                                     alt="{{ $movie->title }}">
                                @if($movie->rating)
                                    <span class="rating-badge">
                                        <i class="bi bi-star-fill"></i> {{ number_format($movie->rating, 1) }}
                                    </span>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $movie->title }}</h5>
                                <p class="card-text text-muted small mb-2">
                                    <i class="bi bi-person-fill"></i> {{ $movie->director }}
                                </p>
                                <p class="card-text text-muted small mb-2">
                                    <i class="bi bi-calendar"></i> {{ $movie->year }} | 
                                    <i class="bi bi-tag"></i> {{ $movie->genre }}
                                </p>
                                <p class="card-text">{{ Str::limit($movie->description, 80) }}</p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-info btn-sm" onclick="showMovie({{ $movie->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm" onclick="editMovie({{ $movie->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteMovie({{ $movie->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> No hay películas registradas. ¡Agrega la primera!
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $movies->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Modal Crear Película -->
    <div class="modal fade" id="createMovieModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('movies.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nueva Película</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Título *</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Director *</label>
                                <input type="text" name="director" class="form-control @error('director') is-invalid @enderror" 
                                       value="{{ old('director') }}" required>
                                @error('director')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Año *</label>
                                <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" 
                                       value="{{ old('year') }}" min="1900" max="{{ date('Y') + 5 }}" required>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Género *</label>
                                <input type="text" name="genre" class="form-control @error('genre') is-invalid @enderror" 
                                       value="{{ old('genre') }}" required>
                                @error('genre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Calificación (0-10)</label>
                                <input type="number" step="0.1" name="rating" class="form-control @error('rating') is-invalid @enderror" 
                                       value="{{ old('rating') }}" min="0" max="10">
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Descripción *</label>
                                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Imagen</label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                       accept="image/*" onchange="previewImage(event, 'createPreview')">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <img id="createPreview" class="mt-2 img-thumbnail" style="max-height: 200px; display: none;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-custom">
                            <i class="bi bi-save"></i> Guardar Película
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Película -->
    <div class="modal fade" id="showMovieModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-eye"></i> Detalles de la Película</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="showMovieContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Película -->
    <div class="modal fade" id="editMovieModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editMovieForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Película</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="editMovieContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-custom">
                            <i class="bi bi-save"></i> Actualizar Película
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Película -->
    <div class="modal fade" id="deleteMovieModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteMovieForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-trash"></i> Eliminar Película</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            ¿Estás seguro de que deseas eliminar esta película?
                        </div>
                        <p class="fw-bold text-center" id="deleteMovieTitle"></p>
                        <p class="text-muted text-center">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview de imagen
        function previewImage(event, previewId) {
            const preview = document.getElementById(previewId);
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }

        // Mostrar película
        function showMovie(id) {
            fetch(`/movies/${id}`)
                .then(response => response.json())
                .then(movie => {
                    const content = `
                        <div class="row">
                            <div class="col-md-5">
                                <img src="${movie.image ? '/storage/' + movie.image : 'https://via.placeholder.com/300x400?text=Sin+Imagen'}" 
                                     class="img-fluid rounded shadow" alt="${movie.title}">
                            </div>
                            <div class="col-md-7">
                                <h3 class="fw-bold mb-3">${movie.title}</h3>
                                ${movie.rating ? `<p class="text-warning fs-4"><i class="bi bi-star-fill"></i> ${parseFloat(movie.rating).toFixed(1)}/10</p>` : ''}
                                <p><strong><i class="bi bi-person-fill"></i> Director:</strong> ${movie.director}</p>
                                <p><strong><i class="bi bi-calendar"></i> Año:</strong> ${movie.year}</p>
                                <p><strong><i class="bi bi-tag"></i> Género:</strong> ${movie.genre}</p>
                                <hr>
                                <h5 class="fw-bold">Descripción:</h5>
                                <p class="text-justify">${movie.description}</p>
                            </div>
                        </div>
                    `;
                    document.getElementById('showMovieContent').innerHTML = content;
                    new bootstrap.Modal(document.getElementById('showMovieModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar la película');
                });
        }

        // Editar película
        function editMovie(id) {
            fetch(`/movies/${id}`)
                .then(response => response.json())
                .then(movie => {
                    document.getElementById('editMovieForm').action = `/movies/${id}`;
                    
                    const content = `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Título *</label>
                                <input type="text" name="title" class="form-control" value="${movie.title}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Director *</label>
                                <input type="text" name="director" class="form-control" value="${movie.director}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Año *</label>
                                <input type="number" name="year" class="form-control" value="${movie.year}" min="1900" max="${new Date().getFullYear() + 5}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Género *</label>
                                <input type="text" name="genre" class="form-control" value="${movie.genre}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Calificación (0-10)</label>
                                <input type="number" step="0.1" name="rating" class="form-control" value="${movie.rating || ''}" min="0" max="10">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Descripción *</label>
                                <textarea name="description" rows="4" class="form-control" required>${movie.description}</textarea>
                            </div>
                            <div class="col-12 mb-3">
                                ${movie.image ? `
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">Imagen Actual:</label><br>
                                        <img src="/storage/${movie.image}" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                ` : ''}
                                <label class="form-label fw-bold">Cambiar Imagen</label>
                                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(event, 'editPreview')">
                                <img id="editPreview" class="mt-2 img-thumbnail" style="max-height: 200px; display: none;">
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('editMovieContent').innerHTML = content;
                    new bootstrap.Modal(document.getElementById('editMovieModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar la película');
                });
        }

        // Eliminar película
        function deleteMovie(id) {
            fetch(`/movies/${id}`)
                .then(response => response.json())
                .then(movie => {
                    document.getElementById('deleteMovieForm').action = `/movies/${id}`;
                    document.getElementById('deleteMovieTitle').textContent = movie.title;
                    new bootstrap.Modal(document.getElementById('deleteMovieModal')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar la película');
                });
        }

        // Reabrir modal si hay errores de validación
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('createMovieModal')).show();
            });
        @endif
    </script>
</body>
</html>