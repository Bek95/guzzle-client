<?php


namespace src\Domain\Account;


interface AccountRepositoryInterface
{
    public function getAccountByPhoneNumber(string $phoneNumber): array;

    public function checkPassword(string $userName, string $password): bool;

}
