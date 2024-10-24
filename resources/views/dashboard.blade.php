@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10  text-center font-weight-bold">
            <H1>GoGreenChain: Recompensas por un Futuro Sostenible</H1>
        </div>
        <div class="col-md-12  text-center">
            <H5>Únete a nuestra plataforma y contribuye a un mundo más verde mientras más ganas recompensas.</H5>
        </div>
        <div class="col-md-12 text-center">
            <button type="button" class="btn btn-success">Regístrate como Empresa</button>                
            <button type="button" class="btn btn-success">Regístrate como Usuario</button>                
        </div><br><br>
        <div class="col-md-12  text-center">
            <H2>Cómo funciona.</H2>
        </div>
            <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas fa-text-width"></i>
                        Para Usuarios<i class="fas fa-check"></i>

                      </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <dl>
                        <dd>Regístrate en la plataforma.</dd>
                        <dd>Realiza acciones sostenibles.</dd>
                        <dd>Gana recompensas.</dd>
                        <dd>Utiliza tu Bitte Wallet.</dd>
                      </dl>
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
                <!-- ./col -->
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">
                        <i class="fas fa-text-width"></i>
                        Para Empresas
                      </h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <dl class="row">
                        <dd class="col-sm-8">Únete como empresa asociada.</dd>
                        <dd class="col-sm-8">Ayudamos a tu empresa así como tú ayudas.</dd>
                        <dd class="col-sm-8">Sé una empresa consiente del medio ambiente.</dd>
                        <dd class="col-sm-8">Mejora tu impacto ambiental.
                        </dd>
                      </dl>
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
                <!-- ./col -->
              </div>
              <div class="col-md-12  text-center">
                <H2>Integración con Blockchain.</H2>
                <H5>Utilizamos tecnología blockchain para garantizar la transparencia y seguridad de tus recompensas.</H5>
            </div>
        </div>
    </div>
</div>
@endsection
