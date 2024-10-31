@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content')
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-user-circle text-primary mr-2"></i>
                        Perfil de {{ $empresa->name }}
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
        <div class="container-fluid">
            <div class="row">
                <!-- Profile Card -->
                <div class="col-md-4">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle elevation-2"
                                     src="{{ $empresa->profile_photo_path ? asset('storage/' . $empresa->profile_photo_path) : asset('storage/default-profile.png') }}"
                                     alt="User profile picture">
                            </div>
                            <h3 class="profile-username text-center">{{ $empresa->name }}</h3>
                            <p class="text-muted text-center">
                                <i class="fas fa-wallet text-info"></i> 
                                <span class="ml-1">{{ $empresa->username_wallet }}</span>
                            </p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b><i class="fas fa-envelope text-info mr-1"></i> Email</b>
                                    <a class="float-right">{{ $empresa->email }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-phone text-info mr-1"></i> Teléfono</b>
                                    <a class="float-right">{{ $empresa->phone }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- About Me Box -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-1"></i>
                                Información Rápida
                            </h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Ubicación</strong>
                            <p class="text-muted">
                                {{ $empresa->city }}, {{ $empresa->state }}
                            </p>
                            <hr>
                            <strong><i class="fas fa-map mr-1"></i> Dirección Completa</strong>
                            <p class="text-muted">
                                {{ $empresa->address }}
                                <br>
                                CP: {{ $empresa->postal_code }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Details Card -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#details" data-toggle="tab">
                                        <i class="fas fa-user-cog mr-1"></i>
                                        Detalles del Perfil
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="details">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <tbody>
                                                <tr>
                                                    <td width="200">
                                                        <strong>
                                                            <i class="fas fa-user text-primary mr-1"></i>
                                                            Nombre Completo
                                                        </strong>
                                                    </td>
                                                    <td>{{ $empresa->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>
                                                            <i class="fas fa-wallet text-primary mr-1"></i>
                                                            Wallet Near
                                                        </strong>
                                                    </td>
                                                    <td>{{ $empresa->username_wallet }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>
                                                            <i class="fas fa-map-marker-alt text-primary mr-1"></i>
                                                            Dirección
                                                        </strong>
                                                    </td>
                                                    <td>{{ $empresa->address }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>
                                                            <i class="fas fa-city text-primary mr-1"></i>
                                                            Ciudad
                                                        </strong>
                                                    </td>
                                                    <td>{{ $empresa->city }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>
                                                            <i class="fas fa-map text-primary mr-1"></i>
                                                            Estado
                                                        </strong>
                                                    </td>
                                                    <td>{{ $empresa->state }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>
                                                            <i class="fas fa-mail-bulk text-primary mr-1"></i>
                                                            Código Postal
                                                        </strong>
                                                    </td>
                                                    <td>{{ $empresa->postal_code }}</td>
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
        </div>
@stop

@section('css')
<style>
    .profile-user-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .profile-username {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 1rem;
    }

    .card {
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 0.5rem;
        border: none;
    }

    .card-primary.card-outline {
        border-top: 3px solid #007bff;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem;
    }

    .list-group-item {
        padding: 1rem;
        border: none;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .card-header {
        background-color: rgba(0,0,0,0.03);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .text-primary {
        color: #007bff !important;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
    }

    .content-header {
        padding: 15px 0.5rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
    }

    .card-body {
        padding: 1.5rem;
    }
</style>
@stop

@section('js')

@stop