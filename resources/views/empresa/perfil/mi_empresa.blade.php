@extends('adminlte::page')

@section('title', 'GoGreenChain')

@section('content_header')
@stop

@section('content')
<!-- Enterprise Profile Card -->
<div class="row">
  <div class="col-md-3">
    <!-- Profile Image Card -->
    <div class="card card-primary">
      <div class="card-body box-profile">
        <div class="text-center">
          <img class="profile-user-img img-fluid img-circle elevation-2"
               src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('storage/default-profile.png') }}"
               alt="Company logo">
        </div>
        <h3 class="profile-username text-center">{{$user->name}}</h3>
        <p class="text-muted text-center">Innovación líder en tecnología blockchain</p>
        
        <ul class="list-group list-group-unbordered mb-3">
          <li class="list-group-item">
            <b><i class="fas fa-calendar-alt"></i> Establecida</b>
            <a class="float-right">{{$user->created_at->format('d/m/Y')}}</a>
          </li>
          <li class="list-group-item">
            <b><i class="fas fa-phone"></i> Contacto</b>
            <a class="float-right">{{$user->phone}}</a>
          </li>
        </ul>

        <button id="editButton" class="btn btn-primary btn-block">
          <i class="fas fa-edit"></i> Editar Empresa
        </button>
      </div>
    </div>

    <!-- About Card -->
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Información de Contacto</h3>
      </div>
      <div class="card-body">
        <strong><i class="fas fa-envelope mr-1"></i> Correo</strong>
        <p class="text-muted">{{$user->email}}</p>
        <hr>
        <strong><i class="fas fa-map-marker-alt mr-1"></i> Ubicación</strong>
        <p class="text-muted">
          {{$user->address}}<br>
          {{$user->city}}, {{$user->state}}<br>
          CP: {{$user->postal_code}}
        </p>
        <button onclick="openLocationInMaps('{{$user->location}}')" class="btn btn-info btn-sm">
          <i class="fas fa-map-marked-alt"></i> Ver Localización
        </button>
      </div>
    </div>
  </div>

  <div class="col-md-9">
    <!-- Main Info Card -->
    <div class="card">
      <div class="card-header p-2">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a class="nav-link active" href="#details" data-toggle="tab">Detalles</a>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-content">
          <!-- Details Tab -->
          <div class="active tab-pane" id="details">
            <div id="userDetails">
              <div class="row">
                <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                  <div class="row">
                    <div class="col-12">
                      <h4>Información General</h4>
                      <div class="post">
                        <div class="info-box bg-light">
                          <div class="info-box-content">
                            <span class="info-box-text text-muted">Dirección Completa</span>
                            <span class="info-box-number text-muted mb-0">
                              {{$user->address}}, {{$user->city}}, {{$user->state}}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Edit Form (Initially Hidden) -->
            <div id="editForm" style="display: none;">
              <form action="{{ route('empresa.actualizar_empresa', $user->id) }}" method="POST" class="form-horizontal" onsubmit="return validateForm(event)">
                @csrf
                @method('PUT')
                
                <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                          <label><i class="fas fa-envelope"></i> Correo Electrónico</label>
                          <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                      </div>
              
                      <div class="form-group">
                          <label><i class="fas fa-map-marker-alt"></i> Dirección</label>
                          <input readonly type="text" name="address" class="form-control" value="{{ $user->address }}" required>
                      </div>
              
                      <div class="form-group">
                          <label>Ciudad</label>
                          <input readonly type="text" name="city" class="form-control" value="{{ $user->city }}" required>
                      </div>
                  </div>
              
                  <div class="col-md-6">
                      <div class="form-group">
                          <label><i class="fas fa-phone"></i> Teléfono</label>
                          <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
                      </div>
              
                      <div class="form-group">
                          <label><i class="fas fa-map"></i> Estado</label>
                          <input readonly type="text" name="state" class="form-control" value="{{ $user->state }}" required>
                      </div>
              
                      <div class="form-group">
                          <label><i class="fas fa-mail-bulk"></i> Código Postal</label>
                          <input readonly type="text" name="postal_code" class="form-control" value="{{ $user->postal_code }}" required>
                      </div>
                  </div>
              </div>
              
              <div class="form-group row">
                  <div class="col-md-6">
                      <label><i class="fas fa-lock"></i> Contraseña</label>
                      <input  type="password" id="password" name="password" class="form-control" required>
                  </div>
              
                  <div class="col-md-6">
                      <label><i class="fas fa-lock"></i> Confirmar Contraseña</label>
                      <input type="password" id="confirmPassword" name="password_confirmation" class="form-control" required>
                  </div>
              </div>

                <input type="hidden" id="location" name="location">

                <div class="row">
                  <div class="col-12">
                    <button type="button" class="btn btn-info mr-2" onclick="obtenerUbicacion()">
                      <i class="fas fa-map-marker-alt"></i> Obtener Ubicación
                    </button>
                    <button type="submit" class="btn btn-success mr-2">
                      <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancelButton">
                      <i class="fas fa-times"></i> Cancelar
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

  <!-- Custom CSS -->
  <style>.profile-user-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border: 3px solid #adb5bd;
    margin: 0 auto;
    padding: 3px;
  }
  
  .list-group-unbordered > .list-group-item {
    border-left: 0;
    border-right: 0;
    border-radius: 0;
    padding-left: 0;
    padding-right: 0;
  }
  
  .card-primary:not(.card-outline) > .card-header {
    background-color: #007bff;
  }
  
  .nav-pills .nav-link.active {
    background-color: #007bff;
  }
  
  .info-box {
    min-height: 100px;
    background: #fff;
    width: 100%;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
    margin-bottom: 1rem;
  }
  
  .info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
  }
  
  .info-box-icon {
    border-radius: 0.25rem 0 0 0.25rem;
    display: block;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
  }
  
  .post {
    border-bottom: 1px solid #adb5bd;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;
  }
  </style>
@stop

@section('css')
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      document.getElementById('editButton').addEventListener('click', function() {
        document.getElementById('userDetails').style.display = 'none'; // Oculta los detalles del usuario
        document.getElementById('editForm').style.display = 'block'; // Muestra el formulario de edición
      });
    
      document.getElementById('cancelButton').addEventListener('click', function() {
        document.getElementById('editForm').style.display = 'none'; // Oculta el formulario de edición
        document.getElementById('userDetails').style.display = 'block'; // Muestra los detalles del usuario
      });
    </script>

  <script>
    let ubicacionObtenida = false;

    function obtenerUbicacion() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(mostrarPosicion, manejarError);
        } else {
            Swal.fire("Error", "La geolocalización no es compatible con este navegador.", "error");
        }
    }

    function mostrarPosicion(position) {
        const latitud = position.coords.latitude;
        const longitud = position.coords.longitude;
        document.getElementById("location").value = `${latitud}, ${longitud}`;
        ubicacionObtenida = true;
    }

    function manejarError(error) {
        Swal.fire("Error", "No se pudo obtener la ubicación. Error: " + error.message, "error");
    }

    function validatePasswords() {
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;

        if (password !== confirmPassword) {
            Swal.fire("Error", "Las contraseñas no coinciden.", "error");
            return false;
        }
        return true;
    }

    function validateForm(event) {
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

  <script>
    function openLocationInMaps(locationString) {
    // Location string is expected to be in "latitude,longitude" format
    const [lat, lng] = locationString.split(',');
    if (lat && lng) {
      // Open Google Maps in a new window
      window.open(`https://www.google.com/maps?q=${lat},${lng}&z=15`, '_blank');
    } else {
      alert('Coordenadas de ubicación no disponibles');
    }
  }
  </script>
@stop