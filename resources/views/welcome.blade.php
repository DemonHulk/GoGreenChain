@extends('adminlte::page')

@section('title', 'GoGreenChain')

@section('content_header')
    <h1 class="text-center">GoGreenChain: Recompensas por un Futuro Sostenible</h1>
@stop

@section('content')
  <div class="container">
      <div class="row justify-content-center">
          <!-- Header Section -->
          <div class="col-md-12 text-center mb-4">
              <p class="lead">
                  Únete a nuestra plataforma y contribuye a un mundo más verde mientras ganas recompensas.
              </p>
              <div class="btn-group">
                  <a href="{{ route('register') }}" class="btn btn-primary btn-lg mx-2">
                  <i class="fas fa-building mr-2"></i>Regístrate como Empresa
                  </a>
                  <a href="{{ route('register') }}" class="btn btn-success btn-lg mx-2">
                      <i class="fas fa-user mr-2"></i>Regístrate como Usuario
                  </a>
              </div>
          </div>

          <!-- Features Section -->
          <div class="col-md-12 mb-4">
              <div class="row">
                  <div class="col-md-4">
                      <div class="small-box bg-info">
                          <div class="inner text-center">
                              <i class="fas fa-leaf fa-3x mb-3"></i>
                              <h4>Impacto Ambiental</h4>
                              <p>Contribuye a la sostenibilidad y reduce tu huella de carbono</p>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="small-box bg-success">
                          <div class="inner text-center">
                              <i class="fas fa-gift fa-3x mb-3"></i>
                              <h4>Gana Recompensas</h4>
                              <p>Obtén tokens y beneficios por tus acciones ecológicas</p>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="small-box bg-warning">
                          <div class="inner text-center">
                              <i class="fas fa-seedling fa-3x mb-4"></i>
                              <h4>Crecimiento Sostenible</h4>
                              <p>Fomenta prácticas empresariales responsables con el medio ambiente</p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- How it Works Section -->
          <div class="col-md-12 mb-4">
              <h2 class="text-center mb-4">Cómo Funciona</h2>
              <div class="row">
                  <div class="col-md-6">
                      <div class="card card-primary">
                          <div class="card-header">
                              <h3 class="card-title">
                                  <i class="fas fa-user mr-2"></i>Para Usuarios
                              </h3>
                          </div>
                          <div class="card-body">
                              <ul class="list-unstyled">
                                  <li><i class="fas fa-check-circle text-success mr-2"></i>Regístrate en la plataforma</li>
                                  <li><i class="fas fa-check-circle text-success mr-2"></i>Realiza acciones sostenibles</li>
                                  <li><i class="fas fa-check-circle text-success mr-2"></i>Gana tokens y recompensas</li>
                                  <li><i class="fas fa-check-circle text-success mr-2"></i>Utiliza tu Bitte Wallet</li>
                              </ul>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="card card-success">
                          <div class="card-header">
                              <h3 class="card-title">
                                  <i class="fas fa-building mr-2"></i>Para Empresas
                              </h3>
                          </div>
                          <div class="card-body">
                              <ul class="list-unstyled">
                                  <li><i class="fas fa-check-circle text-success mr-2"></i>Únete como empresa asociada</li>
                                  <li><i class="fas fa-check-circle text-success mr-2"></i>Ofrece productos/servicios sostenibles</li>
                                  <li><i class="fas fa-check-circle text-success mr-2"></i>Participa en programas de recompensas</li>
                                  <li><i class="fas fa-check-circle text-success mr-2"></i>Mejora tu impacto ambiental</li>
                              </ul>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Blockchain Section -->
          <div class="col-md-12 mb-4">
              <div class="card card-dark">
                  <div class="card-header">
                      <h3 class="card-title text-center w-100">
                          <i class="fas fa-link mr-2"></i>Integración con Blockchain
                      </h3>
                  </div>
                  <div class="card-body text-center">
                      <p class="lead">
                          Utilizamos tecnología blockchain para garantizar la transparencia y seguridad de tus recompensas.
                      </p>
                      <div class="row justify-content-center">
                          <div class="col-md-4">
                              <i class="fas fa-link fa-3x text-primary"></i>
                              <p>Transparencia</p>
                          </div>
                          <div class="col-md-4">
                              <i class="fas fa-shield-alt fa-3x text-success"></i>
                              <p>Seguridad</p>
                          </div>
                          <div class="col-md-4">
                              <i class="fas fa-cube fa-3x text-info"></i>
                              <p>Blockchain</p>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Call to Action -->
          <div class="col-md-12 text-center mb-4">
              <a href="{{ route('login') }}" class="btn btn-lg btn-success">
                  <i class="fas fa-sign-in-alt mr-2"></i>Comienza Ahora
              </a>
          </div>
      </div>
  </div>
@stop

@section('css')
<style>
    .small-box {
        height: 100%;
        padding: 20px;
    }
    .list-unstyled li {
        margin-bottom: 15px;
    }
    .card-title {
        margin-bottom: 0;
    }
</style>
@stop



@section('css')
@stop


