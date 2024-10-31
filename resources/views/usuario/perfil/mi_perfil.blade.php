@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Mi Perfil y Información</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('storage/default-profile.png') }}"
                                 alt="User profile picture">
                        </div>
                        <h3 class="profile-username text-center">{{ $user->name }}</h3>
                        <p class="text-muted text-center">
                            <i class="fas fa-wallet"></i> {{ $user->username_wallet }}
                        </p>
                        <button id="editButton" class="btn btn-primary btn-block">
                            <i class="fas fa-edit mr-2"></i>Editar Perfil
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <!-- User Details Card -->
                <div class="card" id="userDetails">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#details" data-toggle="tab">
                                    <i class="fas fa-user mr-1"></i>Detalles del Usuario
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="details">
                                <dl class="row">
                                    <dt class="col-sm-4"><i class="fas fa-user mr-2"></i>Nombre</dt>
                                    <dd class="col-sm-8">{{ $user->name }}</dd>

                                    <dt class="col-sm-4"><i class="fas fa-wallet mr-2"></i>Wallet Near</dt>
                                    <dd class="col-sm-8">{{ $user->username_wallet }}</dd>

                                    <dt class="col-sm-4"><i class="fas fa-map-marker-alt mr-2"></i>Dirección</dt>
                                    <dd class="col-sm-8">{{ $user->address }}</dd>

                                    <dt class="col-sm-4"><i class="fas fa-city mr-2"></i>Ciudad</dt>
                                    <dd class="col-sm-8">{{ $user->city }}</dd>

                                    <dt class="col-sm-4"><i class="fas fa-map mr-2"></i>Estado</dt>
                                    <dd class="col-sm-8">{{ $user->state }}</dd>

                                    <dt class="col-sm-4"><i class="fas fa-mail-bulk mr-2"></i>Código Postal</dt>
                                    <dd class="col-sm-8">{{ $user->postal_code }}</dd>

                                    <dt class="col-sm-4"><i class="fas fa-phone mr-2"></i>Teléfono</dt>
                                    <dd class="col-sm-8">{{ $user->phone }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div id="editForm" class="card" style="display: none;">
                    <div class="card-header p-2">
                        <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Editar Perfil</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('usuario.perfil.actualizar', $user->id) }}" method="POST" onsubmit="return combinedValidation(event)">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group row mb-3">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-success btn-block mb-3" onclick="obtenerUbicacion()">
                                        <i class="fas fa-map-marker-alt mr-2"></i>Obtener Ubicación
                                    </button>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nombre</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Wallet Near</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-wallet"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="username_wallet" value="{{ $user->username_wallet }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Dirección</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        </div>
                                        <input readonly type="text" class="form-control" name="address" value="{{ $user->address }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Ciudad</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                                        </div>
                                        <input readonly type="text" class="form-control" name="city" value="{{ $user->city }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Estado</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                                        </div>
                                        <input readonly type="text" class="form-control" name="state" value="{{ $user->state }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Código Postal</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                                        </div>
                                        <input readonly type="text" class="form-control" id="editPostalCode" name="postal_code" value="{{ $user->postal_code }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Teléfono</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Contraseña</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" id="password" class="form-control" name="password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility()">
                                                <i id="eyeIcon" class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Confirmar contraseña</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" id="confirmPassword" class="form-control" name="confirm_password" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor: pointer;" onclick="toggleConfirmPasswordVisibility()">
                                                <i id="confirmEyeIcon" class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <small id="passwordError" class="text-danger" style="display: none;">Las contraseñas no coinciden</small>
                                </div>
                            </div>

                            <input type="hidden" id="editLocation" name="location" />
                            
                            <div class="form-group row">
                                <div class="offset-sm-3 col-sm-9">
                                    <div class="btn-group">
                                        <button type="button" id="cancelButton" class="btn btn-default">
                                            <i class="fas fa-times mr-2"></i>Cancelar
                                        </button>
                                        ㅤ
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

@stop

@section('css')
<style>
    .widget-user .widget-user-header {
    padding: 40px;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}

.widget-user .widget-user-username {
    margin-top: 0;
    margin-bottom: 5px;
    font-size: 25px;
    font-weight: 300;
    text-shadow: 0 1px 1px rgba(0,0,0,0.2);
}

.widget-user .widget-user-desc {
    margin-top: 0;
    font-size: 16px;
}

.widget-user .widget-user-image {
    position: absolute;
    top: 65px;
    left: 50%;
    margin-left: -45px;
}

.widget-user .widget-user-image img {
    width: 90px;
    height: 90px;
    border: 3px solid #fff;
}

.widget-user .card-footer {
    padding-top: 50px;
}

.description-block {
    margin: 10px 0;
    text-align: center;
}

.description-block .description-header {
    margin: 0;
    padding: 0;
    font-size: 18px;
    font-weight: 600;
}

.list-group-item {
    border: none;
    padding: 1rem;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.card {
    border: none;
    margin-bottom: 1.5rem;
}

.card-header {
    background-color: transparent;
    padding: 1.25rem;
}

.text-info {
    color: #17a2b8 !important;
}

.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
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
    // Bandera para verificar si el usuario ha obtenido su ubicación
    let ubicacionObtenida = false;

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
        document.getElementById("editLocation").value = `${latitud}, ${longitud}`;
        ubicacionObtenida = true;
    }

    function manejarError(error) {
        alert("Error de geolocalización: " + error.message);
    }

    function validatePasswords() {
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const passwordError = document.getElementById("passwordError");

        if (password !== confirmPassword) {
            passwordError.style.display = "block";
            return false;
        } else {
            passwordError.style.display = "none";
            return true;
        }
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");
        passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        eyeIcon.classList.toggle("fa-eye-slash");
        eyeIcon.classList.toggle("fa-eye");
    }

    function toggleConfirmPasswordVisibility() {
        const confirmPasswordInput = document.getElementById("confirmPassword");
        const confirmEyeIcon = document.getElementById("confirmEyeIcon");
        confirmPasswordInput.type = confirmPasswordInput.type === "password" ? "text" : "password";
        confirmEyeIcon.classList.toggle("fa-eye-slash");
        confirmEyeIcon.classList.toggle("fa-eye");
    }

    // Función combinada que valida tanto la ubicación como las contraseñas
    function combinedValidation(event) {
        if (!ubicacionObtenida) {
            Swal.fire("Atención", "Por favor, obtén tu ubicación antes de enviar el formulario.", "warning");
            event.preventDefault();
            return false;
        }
        
        if (!validatePasswords()) {
            event.preventDefault();
            return false;
        }

        return true;
    }
</script>

@stop