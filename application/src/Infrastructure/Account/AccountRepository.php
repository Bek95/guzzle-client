<?php


namespace src\Infrastructure\Account;


use GuzzleHttp\Client;
use \Illuminate\Support\Facades\Log;
use \App\Exceptions\PasswordException;
use src\Domain\Account\AccountRepositoryInterface;

class AccountRepository implements AccountRepositoryInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * AccountRepository constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $phoneNumber
     * @return array
     */
    public function getAccountByPhoneNumber(string $phoneNumber): array
    {
        try {
            $headers = [
                'Accept' => 'application/json',
            ];

            $response = $this->client->get('/account/' . $phoneNumber,
                [
                    'timeout' => 60.0,
                    'headers' => $headers,
                ]);

            $data = json_decode($response->getBody()->getContents(), true);

            Log::debug('RESPONSE to CALL REST ', $data);

            return $data;


        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param string $userName
     * @param string $password
     * @return bool
     * @throws PasswordException
     */
    public function checkPassword(string $userName, string $password): bool
    {
        try {
            $headers = [
                'Content-Type' => 'application/json'
            ];

            //ici le micro service, attends ce payload
            $data = [
                'userName' => $userName,
                'password' => $password,
                'newPassword' => $password,
            ];

            $result = $this->client->post('/account/change-password',
                [
                    'timeout' => 60.0,
                    'headers' => $headers,
                    'json' => $data,
                    'body' => \GuzzleHttp\json_encode($data),
                ]);

            if ($result->getStatusCode() == 200) {
                return true;
            }

            return false;

        } catch (\Exception $exception) {
            throw new PasswordException($exception->getMessage());
        }
    }

}

