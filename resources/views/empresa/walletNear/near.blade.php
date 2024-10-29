@extends('adminlte::page')

@section('title', 'GoGreenChain')

@section('content_header')
@stop

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/near-api-js@2.1.3/dist/near-api-js.min.js"></script>

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
                <h2 class="display-4 font-weight-bold mb-4 sesion"> <span id="cuenta"></span> </h2>
                <h2 class="display-4 font-weight-bold mb-4 sesion"> <span id="balance"></span> NEAR </h2>

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
    <button id="send-near-button">Enviar NEAR</button>
    {{-- <button id="get-payments-button">Obtener Pagos</button> --}}
    <button id="send-payment-button">Enviar Pago</button>
    <div id="payments"></div>
    <script>
        const config = {
            networkId: 'testnet',
            keyStore: new nearApi.keyStores.BrowserLocalStorageKeyStore(),
            nodeUrl: 'https://rpc.testnet.near.org',
            walletUrl: 'https://testnet.mynearwallet.com/',
            helperUrl: 'https://helper.testnet.near.org',
            explorerUrl: 'https://explorer.testnet.near.org'
        };

        async function initNear() {
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
                    document.getElementById('status').textContent = 'Error: Balance insuficiente.';
                    return;
                }

                const result = await account.sendMoney(receiverAccountId, nearApi.utils.format.parseNearAmount(amount));
                console.log('Resultado de la transacción:', result);

                // Mostrar mensaje de éxito al usuario
                document.getElementById('status').textContent =
                    'Transacción realizada con éxito. ID de la transacción: ' + result.transaction_outcome.id;

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
                document.getElementById('status').textContent = 'Error en la transacción: ' + error.message;
            }
        }

        async function sendPayment(wallet) {
            const account = wallet.account();
            const cuenta = prompt('A quien enviar:');
            const tarea = prompt('Porque:');
            const cantidad = prompt('cuanto:');

            try {
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
                document.getElementById('status').textContent = 'Pago realizado con éxito. ID de la transacción: ' + result.transaction_outcome.id;
            } catch (error) {
                console.error('Error al realizar el pago:', error);
                document.getElementById('status').textContent = 'Error al realizar el pago: ' + error.message;
            }
        }

        async function getPayments(wallet) {
            const { network } = config;
            const provider = new nearApi.providers.JsonRpcProvider('https://rpc.testnet.near.org');

            try {
                const result = await provider.query({
                    request_type: 'call_function',
                    account_id: 'paymentchicha.testnet',
                    method_name: 'get_payments',
                    args_base64: Buffer.from(JSON.stringify({ from_index: 0, limit: 10 })).toString('base64'),
                    finality: 'optimistic',
                });

                const payments = JSON.parse(Buffer.from(result.result).toString());

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

                // Crear la tabla HTML
                let tableHtml = '<table class="table table-striped">';
                tableHtml += '<thead><tr><th>Realizó pago</th><th>Recibió pago</th><th>Cantidad(N)</th><th>Fecha</th><th>Tarea</th></tr></thead>';
                tableHtml += '<tbody>';
                formattedPayments.forEach(payment => {
                    tableHtml += `<tr>
                        <td>${payment.sender}</td>
                        <td>${payment.recipient}</td>
                        <td>${payment.amount}</td>
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

        async function updateUI(wallet) {
            // Obtén todos los elementos con la clase 'mi-clase'
            var sesion = document.getElementsByClassName('sesion');
            var sinsesion = document.getElementsByClassName('sinsesion');

            const loginButton = document.getElementById('login-button');
            const logoutButton = document.getElementById('logout-button');
            const status = document.getElementById('status');

            if (wallet.isSignedIn()) {
                loginButton.style.display = 'none';
                for (var i = 0; i < sesion.length; i++) {
                    sesion[i].style.display = 'block';
                }

                for (var i = 0; i < sinsesion.length; i++) {
                    sinsesion[i].style.display = 'none';
                }

                logoutButton.style.display = 'block';
                const account = wallet.account();
                const balance = await account.getAccountBalance();
                const availableBalance = nearApi.utils.format.formatNearAmount(balance.available);

                document.getElementById('cuenta').textContent = `Wallet: ${wallet.getAccountId()}`;
                document.getElementById('balance').textContent = `Disponible: ${availableBalance}`;

                // // Enviar información de la billetera al backend
                // fetch('/auth/save-wallet', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                //     },
                //     body: JSON.stringify({ accountId: wallet.getAccountId() })
                // });
            } else {
                for (var i = 0; i < sesion.length; i++) {
                    sesion[i].style.display = 'none';
                }

                for (var i = 0; i < sinsesion.length; i++) {
                    sinsesion[i].style.display = 'block';
                }

                loginButton.style.display = 'block';
                logoutButton.style.display = 'none';
                status.textContent = 'No has iniciado sesión';
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

            document.getElementById('send-near-button').onclick = async () => {
                const receiverAccountId = prompt('Ingrese la cuenta del receptor:');
                const amount = prompt('Ingrese la cantidad de NEAR a enviar:');
                await sendNear(wallet, receiverAccountId, amount);
            };

            document.getElementById('send-payment-button').onclick = async () => {
                await sendPayment(wallet);
            };

            // document.getElementById('get-payments-button').onclick = async () => {
                await getPayments(wallet);
            // };

            updateUI(wallet);
        }

        window.onload = initApp;
    </script>

@stop
