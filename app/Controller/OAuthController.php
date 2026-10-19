<?php

namespace App\Controller;

use App\Model\Account;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OAuthController
{
    // Echte Anmeldung; Zugangsdaten kommen aus GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET
    private const PROVIDERS = [
        'google' => [
            'label' => 'Google',
            'env' => 'GOOGLE',
            'authorize_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'token_url' => 'https://oauth2.googleapis.com/token',
            'userinfo_url' => 'https://openidconnect.googleapis.com/v1/userinfo',
            'scope' => 'openid email profile',
        ],
    ];

    // Noch nicht angebunden: nur Hinweisseite
    private const MOCK_PROVIDERS = [
        'microsoft' => 'Microsoft',
        'apple' => 'Apple',
    ];

    public function redirect(Request $request, Response $response, array $args): Response
    {
        $provider = $this->provider($args['provider']);

        if ($provider === null) {
            return $this->fail($response, 'Diese Anmeldung ist noch nicht eingerichtet.');
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'][$args['provider']] = $state;

        $query = http_build_query([
            'client_id' => $provider['client_id'],
            'redirect_uri' => $this->callbackUrl($request, $args['provider']),
            'response_type' => 'code',
            'scope' => $provider['scope'],
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return $response->withHeader('Location', $provider['authorize_url'] . '?' . $query)->withStatus(302);
    }

    public function callback(Request $request, Response $response, array $args): Response
    {
        $provider = $this->provider($args['provider']);
        $params = $request->getQueryParams();
        $expectedState = $_SESSION['oauth_state'][$args['provider']] ?? '';
        unset($_SESSION['oauth_state'][$args['provider']]);

        if ($provider === null) {
            return $this->fail($response, 'Diese Anmeldung ist noch nicht eingerichtet.');
        }

        if (isset($params['error'])) {
            return $this->fail($response, "Anmeldung mit {$provider['label']} abgebrochen.");
        }

        // Schutz gegen untergeschobene Rücksprünge (CSRF)
        if ($expectedState === '' || !hash_equals($expectedState, (string) ($params['state'] ?? ''))) {
            return $this->fail($response, 'Anmeldung abgelaufen, bitte erneut versuchen.');
        }

        $token = $this->httpRequest($provider['token_url'], [
            'grant_type' => 'authorization_code',
            'code' => (string) ($params['code'] ?? ''),
            'redirect_uri' => $this->callbackUrl($request, $args['provider']),
            'client_id' => $provider['client_id'],
            'client_secret' => $provider['client_secret'],
        ]);

        if (empty($token['access_token'])) {
            error_log("MensaPlaner: {$provider['label']}-Token fehlgeschlagen: " . json_encode($token));

            return $this->fail($response, "Anmeldung mit {$provider['label']} fehlgeschlagen.");
        }

        $user = $this->httpRequest($provider['userinfo_url'], null, $token['access_token']);

        if (($user['email_verified'] ?? false) !== true || empty($user['email'])) {
            return $this->fail($response, "{$provider['label']} hat keine bestätigte E-Mail-Adresse geliefert.");
        }

        return $this->loginByEmail($response, $user['email'], $user['name'] ?? $user['email']);
    }

    public function showMock(Request $request, Response $response, array $args): Response
    {
        $label = self::MOCK_PROVIDERS[$args['provider']];

        ob_start();
        require __DIR__ . '/../View/mock-login.php';
        $response->getBody()->write(ob_get_clean());

        return $response;
    }

    private function loginByEmail(Response $response, string $email, string $name): Response
    {
        $accountModel = new Account();
        // Kein Passwort-Login für diese Accounts: zufälliges, unbekanntes Passwort
        $account = $accountModel->findByEmail($email)
            ?? $accountModel->create($name, $email, bin2hex(random_bytes(32)));

        $_SESSION['account_id'] = $account['id'];
        $_SESSION['account_name'] = $account['name'];
        $_SESSION['account_role'] = $account['role'];

        return $response->withHeader('Location', '/speiseplan')->withStatus(302);
    }

    private function provider(string $key): ?array
    {
        $provider = self::PROVIDERS[$key] ?? null;

        if ($provider === null) {
            return null;
        }

        $provider['client_id'] = getenv("{$provider['env']}_CLIENT_ID");
        $provider['client_secret'] = getenv("{$provider['env']}_CLIENT_SECRET");

        if (!$provider['client_id'] || !$provider['client_secret']) {
            return null;
        }

        return $provider;
    }

    private function callbackUrl(Request $request, string $provider): string
    {
        $base = getenv('APP_URL');

        if (!$base) {
            $uri = $request->getUri();
            // Hinter dem Proxy von Render kommt die Anfrage intern per http an
            $scheme = $request->getHeaderLine('X-Forwarded-Proto') ?: $uri->getScheme();
            $port = $uri->getPort() ? ':' . $uri->getPort() : '';
            $base = "{$scheme}://{$uri->getHost()}{$port}";
        }

        return rtrim($base, '/') . "/auth/{$provider}/callback";
    }

    private function httpRequest(string $url, ?array $form, ?string $bearerToken = null): array
    {
        $headers = ['Accept: application/json'];
        $options = ['method' => 'GET', 'ignore_errors' => true, 'timeout' => 10];

        if ($form !== null) {
            $options['method'] = 'POST';
            $options['content'] = http_build_query($form);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }

        if ($bearerToken !== null) {
            $headers[] = "Authorization: Bearer {$bearerToken}";
        }

        $options['header'] = implode("\r\n", $headers);
        $body = @file_get_contents($url, false, stream_context_create(['http' => $options]));

        return json_decode($body ?: '', true) ?: [];
    }

    private function fail(Response $response, string $message): Response
    {
        $_SESSION['flash_error'] = $message;

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
