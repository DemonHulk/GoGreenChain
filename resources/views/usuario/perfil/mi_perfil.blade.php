@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content')
    <body>
        <div class="container-fluid">
            <div class="row">
                <!-- Main Content -->
                <div class="col-md-10">
                    <div class="row justify-content-center mt-5">
                        <div class="col-md-8">
                            <h1 class="font-weight-bold text-center">Perfil del Usuario</h1>
                            <table class="table table-bordered" id="userProfile">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Nombre</td>
                                        <td id="userName">{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Wallet Near</td>
                                        <td id="userWallet">{{ $user->username_wallet }}</td>
                                    </tr>
                                    <tr>
                                        <td>Dirección</td>
                                        <td id="userAddress">{{ $user->address }}</td>
                                    </tr>
                                    <tr>
                                        <td>Ciudad</td>
                                        <td id="userCity">{{ $user->city }}</td>
                                    </tr>
                                    <tr>
                                        <td>Estado</td>
                                        <td id="userState">{{ $user->state }}</td>
                                    </tr>
                                    <tr>
                                        <td>Código Postal</td>
                                        <td id="userPostalCode">{{ $user->postal_code }}</td>
                                    </tr>
                                    <tr>
                                        <td>Teléfono</td>
                                        <td id="userTelephone">{{ $user->phone }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        
                            <div class="text-center">
                                <button id="editButton" class="btn btn-primary">Editar Datos</button>
                            </div>
                        
                            <!-- Formulario de edición oculto -->
                            <div id="editForm" class="mt-4" style="display: none;">
                                <h2 class="font-weight-bold text-center">Editar Perfil</h2>
                                <form id="userEditForm">
                                    @csrf
                                    <div class="form-group">
                                        <label for="editName">Nombre</label>
                                        <input type="text" class="form-control" id="editName" name="name" value="{{ $user->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editWallet">Wallet Near</label>
                                        <input type="text" class="form-control" id="editWallet" name="username_wallet" value="{{ $user->username_wallet }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editAddress">Dirección</label>
                                        <input type="text" class="form-control" id="editAddress" name="address" value="{{ $user->address }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editCity">Ciudad</label>
                                        <input type="text" class="form-control" id="editCity" name="city" value="{{ $user->city }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editState">Estado</label>
                                        <input type="text" class="form-control" id="editState" name="state" value="{{ $user->state }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editPostalCode">Código postal</label>
                                        <input type="text" class="form-control" id="editPostalCode" name="postal_code" value="{{ $user->postal_code }}" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success">Actualizar Datos</button>
                                        <button type="button" id="cancelButton" class="btn btn-secondary">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Historial de Tareas -->
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8">
                            <h5>Historial de Tareas</h5>
                            <div class="card">
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li>Reciclo 5kg de plástico <span class="float-right">2025-03-15</span></li>
                                        <li>Composted 3kg de residuos orgánicos <span class="float-right">2025-03-10</span></li>
                                        <li>Recycled 2kg de papel <span class="float-right">2025-03-05</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Recompensas -->
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8">
                            <h5>Recompensas</h5>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Recompensa</th>
                                                <th>Puntos</th>
                                                <th>Fecha de Obtención</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Botella de Agua Eco-friendly</td>
                                                <td>500</td>
                                                <td>2025-03-20</td>
                                            </tr>
                                            <tr>
                                                <td>Bolsa Reutilizable</td>
                                                <td>300</td>
                                                <td>2025-03-15</td>
                                            </tr>
                                            <tr>
                                                <td>Certificado Plant a Tree</td>
                                                <td>1000</td>
                                                <td>2025-03-01</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://kit.fontawesome.com/42813926db.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Mostrar el formulario de edición al hacer clic en el botón "Editar Datos"
        $('#editButton').click(function() {
            $('#userProfile').slideUp(); // Oculta el perfil
            $('#editForm').slideDown(); // Muestra el formulario
        });

        // Ocultar el formulario de edición y mostrar el perfil al hacer clic en "Cancelar"
        $('#cancelButton').click(function() {
            $('#editForm').slideUp(); // Oculta el formulario
            $('#userProfile').slideDown(); // Muestra el perfil
        });

        // Manejar el envío del formulario
        $('#userEditForm').submit(function(event) {
            event.preventDefault(); // Evita el envío normal del formulario

            // Obtener los datos del formulario
            var name = $('#editName').val();
            var username_wallet = $('#editWallet').val();
            var address = $('#editAddress').val();
            var city = $('#editCity').val();
            var state = $('#editState').val();

            // Aquí puedes hacer una solicitud AJAX para actualizar los datos en el servidor
            $.ajax({
                url: '{{ route("usuario.perfil.actualizar", $user->id) }}', // Cambia esta URL si es necesario
                method: 'PUT',
                data: {
                    _token: $('input[name="_token"]').val(), // Incluye el token CSRF
                    name: name,
                    username_wallet: username_wallet,
                    address: address,
                    city: city,
                    state: state
                },
                success: function(response) {
                    // Actualizar los datos en el perfil
                    $('#userName').text(name);
                    $('#userWallet').text(username_wallet);
                    $('#userAddress').text(address);
                    $('#userCity').text(city);
                    $('#userState').text(state);
                    
                    // Ocultar el formulario de edición y mostrar el perfil
                    $('#editForm').slideUp();
                    $('#userProfile').slideDown();
                },
                error: function(xhr) {
                    // Manejo de errores
                    alert('Ocurrió un error al actualizar los datos.');
                }
            });
        });
    });
</script>

@stop
