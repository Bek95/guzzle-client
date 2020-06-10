<?php


namespace App\Providers\Domain\Account;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\ServiceProvider;
use Sainsburys\Guzzle\Oauth2\GrantType\ClientCredentials;
use Sainsburys\Guzzle\Oauth2\GrantType\RefreshToken;
use Sainsburys\Guzzle\Oauth2\Middleware\OAuthMiddleware;
use src\Domain\Account\AccountRepositoryInterface;
use src\Infrastructure\Account\AccountRepository;

class AccountServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(AccountRepository::class,
            function () {

                $config = [
                    ClientCredentials::CONFIG_CLIENT_SECRET => env('USER_CLIENT_SECRET'),
                    ClientCredentials::CONFIG_CLIENT_ID => env('USER_CLIENT_ID'),
                ];

                $oauthClient = new Client(['base_uri' => env('URL_OAUTH2')]);
                $grantType = new ClientCredentials($oauthClient, $config);
                $refreshToken = new RefreshToken($oauthClient, $config);
                $middleware = new OAuthMiddleware($oauthClient, $grantType, $refreshToken);

                $handlerStack = HandlerStack::create();
                $handlerStack->push($middleware->onBefore());
                $handlerStack->push($middleware->onFailure(5));

                $client = new Client(
                    [
                        'handler' => $handlerStack,
                        'base_uri' => env('REST_MICRO_SERVICE_BASE_URI'),
                        'auth' => 'oauth2',
                    ]
                );

                return new AccountRepository($client);
            });

    }

}
