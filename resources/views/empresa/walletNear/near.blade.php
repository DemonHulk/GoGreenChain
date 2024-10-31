@extends('adminlte::page')

@section('title', 'GoGreenChain')

@section('content_header')
@stop

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/near-api-js@2.1.3/dist/near-api-js.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="card card-primary card-outline my-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-wallet mr-2"></i>
                Cartera Wallet Empresa
            </h3>
        </div>
        <div class="card-body">
            <!-- Balance Amount -->
            <div class="text-center mb-5">
                <h2 class="display-4 font-weight-bold mb-4 sinsesion"> INICIA SESIÓN </h2>
                <h2 class="display-4 font-weight-bold mb-4 " id="cargando"> CARGANDO WALLET... </h2>
                <h2 class="display-4 font-weight-bold mb-4 sesion"> <span id="cuenta"></span> </h2>
                <h2 class="display-4 font-weight-bold mb-4 sesion"> <span id="balance"></span> </h2>
                <h2 class="display-4 font-weight-bold mb-4 sesion"> <span id="balanceMXN"></span> </h2>
                <h3 class="display-6 font-weight-bold mb-4 sesion"> <span id="valorMXNNEar"></span> </h3>

                <p class="text-muted sesion">
                    Balance disponible
                    <i class="fas fa-info-circle" data-toggle="tooltip" title="Balance actual en su cuenta"></i>
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-md-12 sinsesion">
                    <div class="text-center mb-3">
                        <button id="login-button" class="btn btn-success btn-lg btn-block login-button">
                            Iniciar sesión
                        </button>
                    </div>
                </div>
                <div class="col-md-12 sesion">
                    <div class="text-center mb-3">
                        <button id="logout-button" class="btn btn-success btn-lg btn-block logout-button">
                            Cerrar sesión
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <button id="send-near-button">Enviar NEAR</button> --}}
    {{-- <button id="get-payments-button">Obtener Pagos</button> --}}

    <div class=" sesion">
        <h2 class="display-5 font-weight-bold mb-4 sesion">Pagar tareas completadas.</h2>
        <div id="completed-tasks">
            @if ($completadaTasks->isEmpty())
                <p>No hay tareas completadas.</p>
            @else
                <table class="table table-bordered table-hover bg-white">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Usuario</th>
                            <th>Recompensa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($completadaTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->usuario->name }}</td>
                                <td>{{ number_format($task->reward, 2) }}N</td>
                                <td>
                                    <button class="btn btn-primary pagar-tarea"
                                            data-username-wallet="{{ $task->usuario->username_wallet }}"
                                            data-title="{{ $task->title }}"
                                            data-reward="{{ number_format($task->reward, 2) }}">
                                        Pagar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <h2 class="display-5 font-weight-bold mb-4 sesion">Historial de pagos registrados con la Wallet.</h2>

    <div id="payments"></div>

    <script>
        async function getNearMXN() {
            var sesion = document.getElementsByClassName('sesion');
            for (var i = 0; i < sesion.length; i++) {
                sesion[i].style.display = 'none';
            }
            try {
                const response = await fetch(
                    'https://api.coingecko.com/api/v3/simple/price?ids=near&vs_currencies=mxn');
                const data = await response.json();
                localStorage.setItem('nearmxn', data.near.mxn);
                for (var i = 0; i < sesion.length; i++) {
                    sesion[i].style.display = 'block';
                }
                return data.near.mxn;
            } catch (error) {
                for (var i = 0; i < sesion.length; i++) {
                    sesion[i].style.display = 'block';
                }
                return localStorage.getItem('nearmxn');
            }
        }

        const config = {
            networkId: 'testnet',
            keyStore: new nearApi.keyStores.BrowserLocalStorageKeyStore(),
            nodeUrl: 'https://rpc.testnet.near.org',
            walletUrl: 'https://testnet.mynearwallet.com/',
            helperUrl: 'https://helper.testnet.near.org',
            explorerUrl: 'https://explorer.testnet.near.org'
        };

        async function initNear() {
            var sesion = document.getElementsByClassName('sesion');
            var sinsesion = document.getElementsByClassName('sinsesion');
            for (var i = 0; i < sesion.length; i++) {
                sesion[i].style.display = 'none';
            }

            for (var i = 0; i < sinsesion.length; i++) {
                sinsesion[i].style.display = 'none';
            }
            const near = await nearApi.connect(config);
            const wallet = new nearApi.WalletConnection(near, 'mi-app'); // Prefijo genérico para la app
            return wallet;
        }

        async function sendNear(wallet, receiverAccountId, amount) {
            const account = wallet.account();
            try {
                // Obtener el balance de la cuenta
                const balance = await account.getAccountBalance();
                const availableBalance = nearApi.utils.format.formatNearAmount(balance.available);

                // Verificar si el balance es suficiente
                if (parseFloat(availableBalance) < parseFloat(amount)) {
                    alert('Error: Balance insuficiente.');
                    return;
                }

                const result = await account.sendMoney(receiverAccountId, nearApi.utils.format.parseNearAmount(amount));
                console.log('Resultado de la transacción:', result);

                // Enviar detalles de la transacción al backend
                const response = await fetch('/log-transaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        receiver_account_id: receiverAccountId,
                        amount: amount,
                        transaction_id: result.transaction_outcome.id
                    })
                });

                const data = await response.json();
                console.log(data);

            } catch (error) {
                console.error('Error en la transacción:', error);
            }
        }

        async function sendPayment(wallet, cuenta, id, tarea,cantidad) {
            const account = wallet.account();
            const balance = await account.getAccountBalance();
            const availableBalance = nearApi.utils.format.formatNearAmount(balance.available);

            if (parseFloat(availableBalance) < parseFloat(cantidad)) {
                alert('Error: Balance insuficiente.');
                return;
            }

            try {
                await marcarTareaComoPagada(id);

                const result = await account.functionCall({
                    contractId: 'paymentchicha.testnet',
                    methodName: 'pay',
                    args: {
                        recipient: cuenta,
                        mensaje: tarea
                    },
                    gas: '30000000000000',
                    attachedDeposit: nearApi.utils.format.parseNearAmount(cantidad)
                });
                console.log('Resultado de la llamada al contrato:', result);
                // document.getElementById('status').textContent = 'Pago realizado con éxito. ID de la transacción: ' + result.transaction_outcome.id;
            } catch (error) {
                console.error('Error al realizar el pago:', error);
                // document.getElementById('status').textContent = 'Error al realizar el pago: ' + error.message;
            }
        }

        async function marcarTareaComoPagada(id) {
            try {
                const response = await fetch(`/empresa/walletNear/tareas/pagar_tarea/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ pagado: true })
                });

                const data = await response.json();

            } catch (error) {
                console.error('Error while marking the task as paid in the backend:', error);
            }
        }

        async function getPayments(wallet) {
            const provider = new nearApi.providers.JsonRpcProvider('https://rpc.testnet.near.org');

            try {
                const result = await provider.query({
                    request_type: 'call_function',
                    account_id: 'paymentchicha.testnet',
                    method_name: 'get_payments',
                    args_base64: Buffer.from(JSON.stringify({
                        from_index: 0,
                        limit: 10000
                    })).toString('base64'),
                    finality: 'optimistic',
                });
                var payments;
                try {
                    payments = JSON.parse(Buffer.from(result.result).toString());
                    localStorage.setItem('paymentsList', JSON.stringify(payments));
                } catch {
                    payments = JSON.parse(localStorage.getItem('paymentsList'));
                }

                // Formatear los datos
                const formattedPayments = payments.map(payment => {
                    const date = new Date(Number(payment.timestamp) / 1e6); // Convertir timestamp a milisegundos
                    const formattedDate = date.toLocaleString(); // Formatear la fecha
                    const formattedAmount = nearApi.utils.format.formatNearAmount(payment.amount); // Formatear el amount
                    return {
                        ...payment,
                        timestamp: formattedDate,
                        amount: formattedAmount
                    };
                });

                const nearPriceMXN = await getNearMXN();

                // Crear la tabla HTML
                let tableHtml = '<table class="table table-bordered table-hover bg-white">';
                tableHtml += '<thead><tr><th>Realizó pago</th><th>Recibió pago</th><th>Cantidad(N)</th><th>Valor(MXN)</th><th>Fecha</th><th>Tarea</th></tr></thead>';
                tableHtml += '<tbody>';
                formattedPayments.forEach(payment => {
                    tableHtml += `<tr>
                        <td>${payment.sender}</td>
                        <td>${payment.recipient}</td>
                        <td>${payment.amount}</td>
                        <td>$${(parseFloat(payment.amount) * nearPriceMXN).toFixed(2)}</td>
                        <td>${payment.timestamp}</td>
                        <td>${payment.mensaje}</td>
                    </tr>`;
                });
                tableHtml += '</tbody></table>';

                document.getElementById('payments').innerHTML = tableHtml;
            } catch (error) {
                console.error('Error al obtener los pagos del contrato:', error);
                document.getElementById('payments').textContent = 'Error al obtener los pagos del contrato: ' + error.message;
            }
        }

        async function getcompletadaTasks() {
            try {
                const nearPriceMXN = await getNearMXN();

                const completadaTasks = @json($completadaTasks);

                // Crear la tabla HTML
                let tableHtml = '<table class="table table-bordered table-hover bg-white">';
                tableHtml += '<thead><tr><th>ID</th><th>Título</th><th>Usuario</th><th>wallet</th><th>Recompensa</th><th>Pagar</th></tr></thead>';
                tableHtml += '<tbody>';

                if (completadaTasks.length === 0) {
                    tableHtml += `<tr><td colspan="6" class="text-center">No hay tareas completadas pendientes de pago.</td></tr>`;
                } else {
                completadaTasks.forEach(task => {
                    tableHtml += `<tr>
                        <td>${task.id}</td>
                        <td>${task.title}</td>
                        <td>${task.usuario ? task.usuario.name : 'Sin asignar'}</td>
                        <td>${task.usuario ? task.usuario.username_wallet : 'Sin asignar'}</td>
                        <td>${parseFloat(task.reward).toFixed(2)} NEAR ($${(parseFloat(parseFloat(task.reward).toFixed(2)) * nearPriceMXN).toFixed(2)} MXN)</td>
                        <td>
                            <button class="btn btn-primary pagar-tarea"
                                    data-username-wallet="${task.usuario ? task.usuario.username_wallet : 'Sin asignar'}"
                                    data-title="${task.title}"
                                    data-id="${task.id}"
                                    data-reward="${parseFloat(task.reward).toFixed(2)}">
                                Pagar
                            </button>
                        </td>
                    </tr>`;
                });
                }

                tableHtml += '</tbody></table>';

                document.getElementById('completed-tasks').innerHTML = tableHtml;

            } catch (error) {
                console.error('Error al obtener las tareas completadas:', error);
                document.getElementById('completed-tasks').textContent = 'Error al obtener las tareas completadas: ' + error.message;
            }
        }


        // Llamar a la función cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', getcompletadaTasks);

        async function updateUI(wallet) {
            var sesion = document.getElementsByClassName('sesion');
            var sinsesion = document.getElementsByClassName('sinsesion');

            const loginButton = document.getElementById('login-button');
            const logoutButton = document.getElementById('logout-button');
            const cargando = document.getElementById('cargando');

            cargando.style.display = 'block';
            if (wallet.isSignedIn()) {
                loginButton.style.display = 'none';
                logoutButton.style.display = 'block';
                const account = wallet.account();
                const balance = await account.getAccountBalance();
                const availableBalance = nearApi.utils.format.formatNearAmount(balance.available);

                document.getElementById('cuenta').textContent = `Wallet: ${wallet.getAccountId()}`;
                document.getElementById('balance').textContent = `Disponible: ${parseFloat(availableBalance).toFixed(5)} NEAR`;
                const nearPriceMXN = await getNearMXN();
                document.getElementById('balanceMXN').textContent = `Valor: $${(parseFloat(availableBalance) * nearPriceMXN).toFixed(2)} MXN`;
                document.getElementById('valorMXNNEar').textContent = `1 NEAR : $${nearPriceMXN.toFixed(2)} MXN`;
                cargando.style.display = 'none';

                setTimeout(() => {
                    for (var i = 0; i < sinsesion.length; i++) {
                        sinsesion[i].style.display = 'none';
                    }
                    for (var i = 0; i < sesion.length; i++) {
                        sesion[i].style.display = 'block';
                    }
                }, 500);
            } else {
                cargando.style.display = 'none';
                for (var i = 0; i < sesion.length; i++) {
                    sesion[i].style.display = 'none';
                }
                for (var i = 0; i < sinsesion.length; i++) {
                    sinsesion[i].style.display = 'block';
                }
                loginButton.style.display = 'block';
                logoutButton.style.display = 'none';
            }
        }

        async function initApp() {
            const wallet = await initNear();

            document.getElementById('login-button').onclick = async () => {
                await wallet.requestSignIn({
                    // No es necesario un contractId a menos que sea para interacción con un contrato específico
                    successUrl: window.location.href,
                    failureUrl: window.location.href
                });
            };

            document.getElementById('logout-button').onclick = async () => {
                wallet.signOut();
                updateUI(wallet);
            };

            // document.getElementById('send-near-button').onclick = async () => {
            //     const receiverAccountId = prompt('Ingrese la cuenta del receptor:');
            //     const amount = prompt('Ingrese la cantidad de NEAR a enviar:');
            //     await sendNear(wallet, receiverAccountId, amount);
            // };

            // Agregar evento onclick a los botones de pagar tarea
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('pagar-tarea')) {
                    const button = event.target;
                    const cuenta = button.getAttribute('data-username-wallet');
                    const id = button.getAttribute('data-id');
                    const tarea = button.getAttribute('data-title');
                    const cantidad = button.getAttribute('data-reward');
                    sendPayment(wallet, cuenta, id,tarea, cantidad);

                }
            });

            await getPayments(wallet);
            updateUI(wallet);
        }

        window.onload = initApp;
    </script>

@stop
