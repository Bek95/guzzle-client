<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use src\Domain\Account\AccountRepositoryInterface;

class AccountController extends Controller
{

    /**
     * @var AccountRepositoryInterface
     */
    private $myClassRepository;

    /**
     * MyClassController constructor.
     * @param AccountRepositoryInterface $myClassRepository
     */
    public function __construct(AccountRepositoryInterface $myClassRepository)
    {
        $this->myClassRepository = $myClassRepository;
    }

    /**
     * @param $phoneNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccountByPhoneNumber($phoneNumber)
    {
        $resultat = $this->myClassRepository->getAccountByPhoneNumber($phoneNumber);

        $response = [
            'userName' => $resultat['phoneNumber'],
            'email' => $resultat['email'],
            'firstName' => $resultat['firstName'],
            'lastName' => $resultat['lastName'],
            'enabled' => true,
            'emailVerified' => true,
            'attributes' => [
                'position' => ['user'],
                'likes' => []
            ],
            'roles' => [
                'admin'
            ]
        ];

        return response()->json($response, 200);
    }

    /**
     * @param $phoneNumber
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPassword($phoneNumber, Request $request)
    {
        $password = $request->all();

        $params = [
            'phoneNumber' => $phoneNumber,
            'password' => $password['password'],
        ];

        $response = $this->myClassRepository->checkPassword($params['phoneNumber'], $params['password']);

        return response()->json($response, 200);
    }

}
