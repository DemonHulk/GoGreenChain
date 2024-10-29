
@section('css')
@stop
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEAR Send</title>
    <script src="https://cdn.jsdelivr.net/npm/near-api-js@2.1.3/dist/near-api-js.min.js"></script>
</head>
<body>
    <button id="login-button">Iniciar Sesión con NEAR</button>
    <button id="send-near-button">Enviar NEAR</button>
    <button id="view-contract-button">Ver Contrato</button>
    <button id="call-contract-button">Llamar Contrato</button>
    <button id="get-logs-button">Obtener Logs</button>
    <button id="logout-button" style="display:none;">Cerrar Sesión</button>
    <div id="status"></div>
    <div id="logs"></div>
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
                document.getElementById('status').textContent = 'Transacción realizada con éxito. ID de la transacción: ' + result.transaction_outcome.id;

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

        async function viewContract(wallet, contractId, methodName, args) {
            const account = wallet.account();
            try {
                const result = await account.viewFunction(contractId, methodName, args);
                console.log('Resultado del contrato:', result);
                document.getElementById('status').textContent = 'Resultado del contrato: ' + JSON.stringify(result);
            } catch (error) {
                console.error('Error al ver el contrato:', error);
                document.getElementById('status').textContent = 'Error al ver el contrato: ' + error.message;
            }
        }

        async function callContract(wallet, contractId, methodName, args, gas = '30000000000000', deposit = '0') {
            const account = wallet.account();
            try {
                const result = await account.functionCall({
                    contractId,
                    methodName,
                    args,
                    gas,
                    attachedDeposit: nearApi.utils.format.parseNearAmount(deposit)
                });
                console.log('Resultado de la llamada al contrato:', result);
                document.getElementById('status').textContent = 'Llamada al contrato realizada con éxito. ID de la transacción: ' + result.transaction_outcome.id;
            } catch (error) {
                console.error('Error al llamar al contrato:', error);
                document.getElementById('status').textContent = 'Error al llamar al contrato: ' + error.message;
            }
        }

        async function getContractLogs(wallet, contractId) {
            const account = wallet.account();
            try {
                const result = await account.viewFunction(contractId, 'get_messages', {});
                console.log('Logs del contrato:', result);
                document.getElementById('logs').textContent = 'Logs del contrato: ' + JSON.stringify(result);
            } catch (error) {
                console.error('Error al obtener los logs del contrato:', error);
                document.getElementById('logs').textContent = 'Error al obtener los logs del contrato: ' + error.message;
            }
        }

        function updateUI(wallet) {
            const loginButton = document.getElementById('login-button');
            const logoutButton = document.getElementById('logout-button');
            const status = document.getElementById('status');

            if (wallet.isSignedIn()) {
                loginButton.style.display = 'none';
                logoutButton.style.display = 'block';
                status.textContent = `Conectado como: ${wallet.getAccountId()}`;
                // Enviar información de la billetera al backend
                fetch('/auth/save-wallet', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ accountId: wallet.getAccountId() })
                });
            } else {
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

            document.getElementById('view-contract-button').onclick = async () => {
                const contractId = prompt('Ingrese el ID del contrato:');
                const methodName = prompt('Ingrese el nombre del método de vista:');
                const args = JSON.parse(prompt('Ingrese los argumentos en formato JSON:'));
                await viewContract(wallet, contractId, methodName, args);
            };

            document.getElementById('call-contract-button').onclick = async () => {
                const contractId = prompt('Ingrese el ID del contrato:');
                const methodName = prompt('Ingrese el nombre del método de cambio:');
                const args = JSON.parse(prompt('Ingrese los argumentos en formato JSON:'));
                const gas = prompt('Ingrese la cantidad de gas (opcional):', '30000000000000');
                const deposit = prompt('Ingrese la cantidad de NEAR a depositar (opcional):', '0');
                await callContract(wallet, contractId, methodName, args, gas, deposit);
            };

            document.getElementById('get-logs-button').onclick = async () => {
                const contractId = prompt('Ingrese el ID del contrato:');
                await getContractLogs(wallet, contractId);
            };

            updateUI(wallet);
        }

        window.onload = initApp;
    </script>
</body>
</html>
