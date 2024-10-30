@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Profile Header -->
            <div class="card bg-gradient-primary text-white mb-4">
                <div class="card-body py-5">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-circle">
                                <img class="profile-user-img img-fluid img-circle"
                                src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('storage/default-profile.png') }}"
                                alt="Company logo">
                            </div>
                        </div>
                        <div class="col text-start">
                            <h1 class="mb-1 font-weight-bold" id="userName">{{ $user->name }}</h1>
                            <p class="mb-0"><i class="fas fa-wallet me-2"></i>{{ $user->username_wallet }}</p>
                        </div>
                        <div class="col-auto">
                            <button id="editButton" class="btn btn-light px-4">
                                <i class="fas fa-edit me-2"></i>Editar Perfil
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="row">
                <div class="col-md-12">
                    <!-- User Details Card -->
                    <div class="card mb-4" id="userDetails">
                        <div class="card-header border-0 bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Detalles del Usuario</h5>
                        </div>
                        <div class="card-body">
                            <div class="detail-item">
                                <span class="detail-label">Nombre</span>
                                <span class="detail-value" id="userName">{{ $user->name }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Wallet Near</span>
                                <span class="detail-value" id="userWallet">{{ $user->username_wallet }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Dirección</span>
                                <span class="detail-value" id="userAddress">{{ $user->address }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Ciudad</span>
                                <span class="detail-value" id="userCity">{{ $user->city }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Estado</span>
                                <span class="detail-value" id="userState">{{ $user->state }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Código Postal</span>
                                <span class="detail-value" id="userPostalCode">{{ $user->postal_code }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Teléfono</span>
                                <span class="detail-value" id="userTelephone">{{ $user->phone }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <div id="editForm" class="card mb-4" style="display: none;">
                        <div class="card-header border-0 bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Editar Perfil</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('usuario.perfil.actualizar', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <!-- Botón para obtener ubicación -->
                                <button type="button" class="btn btn-success" onclick="obtenerUbicacion()">Obtener Ubicación</button>
                                <div class="form-group mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Wallet Near</label>
                                    <input type="text" class="form-control" name="username_wallet" value="{{ $user->username_wallet }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Dirección (Favor de obtener los datos por el botón Obtener Ubicación)</label>
                                    <input readonly type="text" class="form-control" name="address" value="{{ $user->address }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Ciudad</label>
                                    <input readonly type="text" class="form-control" name="city" value="{{ $user->city }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Estado</label>
                                    <input readonly type="text" class="form-control" name="state" value="{{ $user->state }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Código postal</label>
                                    <input readonly type="text" class="form-control" id="editPostalCode" name="postal_code" value="{{ $user->postal_code }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" required>
                                </div>
                                <!-- Campo para almacenar la ubicación -->
                                <input type="hidden" id="editLocation" name="location" />
                                        
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" id="cancelButton" class="btn btn-light px-4">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* General Styles */
.card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,.08);
    transition: transform 0.2s;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
}

/* Avatar Circle */
.avatar-circle {
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    border: 3px solid rgba(255,255,255,0.3);
}

/* Detail Items */
.detail-item {
    padding: 12px 0;
    border-bottom: 1px solid rgba(0,0,0,.05);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    display: block;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 4px;
}

.detail-value {
    color: #2d3748;
    font-weight: 500;
}

/* Timeline */
.timeline {
    position: relative;
    padding: 1rem;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    padding-bottom: 1.5rem;
}

.timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.timeline-content {
    padding: 0.5rem 0;
}

/* Reward Cards */
.reward-card {
    text-align: center;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    transition: transform 0.2s;
}

.reward-card:hover {
    transform: translateY(-5px);
    background: #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,.08);
}

.reward-icon {
    font-size: 2rem;
    color: #4e73df;
    margin-bottom: 1rem;
}

.reward-points {
    color: #6c757d;
    font-size: 0.875rem;
    margin: 0.5rem 0;
}

/* Form Styling */
.form-control {
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
    padding: 0.75rem 1rem;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78,115,223,.25);
}

.form-label {
    color: #4a5568;
    font-weight: 500;
}

/* Buttons */
.btn {
    border-radius: 0.5rem;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
}

.btn-primary {
    background: #4e73df;
    border-color: #4e73df;
}

.btn-primary:hover {
    background: #224abe;
    border-color: #224abe;
}

/* Badges */
.badge {
    padding: 0.5rem 1rem;
    font-weight: 500;
    border-radius: 0.5rem;
}
</style>
@stop

@section('js')
<script src="https://kit.fontawesome.com/42813926db.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('editButton').addEventListener('click', function() {
        document.getElementById('userDetails').style.display = 'none';
        document.getElementById('editForm').style.display = 'block';
    });

    document.getElementById('cancelButton').addEventListener('click', function() {
        document.getElementById('editForm').style.display = 'none';
        document.getElementById('userDetails').style.display = 'block';
    });
</script>

<script>
    function obtenerUbicacion() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(mostrarPosicion, manejarError);
        } else {
            alert("La geolocalización no es compatible con este navegador.");
        }
    }

    function mostrarPosicion(position) {
        const latitud = position.coords.latitude;
        const longitud = position.coords.longitude;
        document.getElementById("editLocation").value = `${latitud}, ${longitud}`; // Almacena latitud y longitud
        obtenerDireccion(latitud, longitud);
    }

    function obtenerDireccion(lat, lon) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const direccion = data.display_name;
                const ciudad = data.address.city || data.address.town || data.address.village || "No disponible";
                const estado = data.address.state || "No disponible";
                const cp = data.address.postcode || "No disponible";

                document.querySelector("input[name='address']").value = direccion; // Completa el campo de dirección
                document.querySelector("input[name='city']").value = ciudad; // Completa el campo de ciudad
                document.querySelector("input[name='state']").value = estado; // Completa el campo de estado
                document.getElementById("editPostalCode").value = cp; // Completa el campo de código postal
            })
            .catch(error => {
                console.error("Error al obtener la dirección:", error);
            });
    }

    function manejarError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("Usuario denegó la solicitud de Geolocalización.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("La ubicación no está disponible.");
                break;
            case error.TIMEOUT:
                alert("La solicitud para obtener la ubicación ha expirado.");
                break;
            case error.UNKNOWN_ERROR:
                alert("Ocurrió un error desconocido.");
                break;
        }
    }
</script>

@stop