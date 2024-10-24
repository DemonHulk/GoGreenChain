@extends('adminlte::page')

@section('title', 'GoGreenChain')

@section('content_header')
@stop

@section('content')
    <!-- Balance Box -->
<div class="card">
    <div class="card-header">
      <h3 class="card-title">Balances</h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
      </div>
    </div>
    <div class="card-body text-center">
      <!-- Balance Amount -->
      <?php
      // Convertir yoctoNEAR a NEAR dividiendo entre 10^24
      $nearBalance = bcdiv($balance, '1000000000000000000000000', 5);
      ?>
      <h2 class="display-4 font-weight-bold mb-4"> {{ $nearBalance }} NEAR </h2>
      <p class="text-muted">Balance disponible <i class="fas fa-info-circle"></i></p>
      
    </div>
    
    <!-- Portfolio Section -->
    @if (auth()->user()->rol->tipo === 'Usuario')
    <div class="card-footer">
      <h5 class="mb-3">Tareas disponibles cercanas</h5>
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <div class="bg-light rounded-circle p-2 mr-2">
            <i class="fas fa-leaf"></i>
          </div>
          <div>
            <h6 class="mb-0">Recoger plásticos</h6>
            <small class="text-muted">$15</small><br>
            <small class="text-muted">Localización: Santiago ixcuintla</small>
          </div>
        </div>
        <div class="text-right">
          <h6 class="mb-0">0.10361</h6>
          <small class="text-muted">$15</small><br>
          <button class="button btn-primary">Ver tarea</button>
         </div>
      </div>
    </div>
    @endif

  </div>
  
  <!-- Required CSS -->
  <style>
  .btn-lg.rounded-circle {
    width: 60px;
    height: 60px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .card-body {
    padding: 2rem;
  }
  </style>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
  <script>// Ejemplo en JavaScript usando near-api-js

    const { keyStores, Near, WalletConnection, utils } = require('near-api-js');
    
    // Configuración de conexión con testnet
    const nearConfig = {
      networkId: "testnet",
      keyStore: new keyStores.BrowserLocalStorageKeyStore(), // Puedes cambiar la forma en que almacenas las claves
      nodeUrl: "https://rpc.testnet.near.org",
      walletUrl: "https://wallet.testnet.near.org",
      helperUrl: "https://helper.testnet.near.org",
      explorerUrl: "https://explorer.testnet.near.org"
    };
    
    async function sendNearTransaction(sender, receiver, amount) {
      // Inicializar Near
      const near = new Near(nearConfig);
      
      // Conectar el wallet
      const wallet = new WalletConnection(near);
    
      // Convertir NEAR a yoctoNEAR
      const amountInYocto = utils.format.parseNearAmount(amount);
    
      // Enviar la transacción firmada
      const result = await wallet.account().sendMoney(receiver, amountInYocto);
      
      console.log('Transacción exitosa:', result.transaction.hash);
    }
    </script>
@stop