<?php

namespace App\Services\User;

use App\Entity\ForgetPassword;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Entity\UserRegister;
use App\Repository\ForgetPasswordRepository;
use App\Repository\UserRegisterRepository;
use App\Services\MailService;
use App\Services\Token\ForgetTokenService;

class AuthService
{
	private UserRepository $userRepository;
	private UserRegisterRepository $userRegisterRepository;
	private ForgetPasswordRepository $forgetPasswordRepository;

	public function __construct(
		UserRepository $userRepository,
		UserRegisterRepository $userRegisterRepository,
		ForgetPasswordRepository $forgetPasswordRepository
	) {
		$this->userRepository = $userRepository;
		$this->userRegisterRepository = $userRegisterRepository;
		$this->forgetPasswordRepository = $forgetPasswordRepository;
	}

	private const timeoutRegisterUser = 600;

	/**
	 * @param array[firstName: string, name: string, email: string, password: string] $data
	 * @return User
	 */
	public function register(UserRegister $userRegister)
	{
		if ($this->userRepository->getByEmail($userRegister->getEmail())) {
			throw new \Exception("Cette adresse email est déjà utilisé.");
		}

		$userRegisterFinded = $this->userRegisterRepository->getByEmail(
			$userRegister->getEmail()
		);

		$now = new \DateTime("now");

		if (
			$userRegisterFinded &&
			$userRegisterFinded->getCreatedAt()->getTimestamp() +
				self::timeoutRegisterUser <
				$now->getTimestamp()
		) {
			$this->userRegisterRepository->delete($userRegisterFinded);
			$userRegisterFinded = null;
		}

		if ($userRegisterFinded) {
			throw new \Exception("Un email à déjà été envoyé a cette adresse.");
		}

		$userRegister->setPassword(
			password_hash($userRegister->getPassword(), PASSWORD_DEFAULT)
		);
		$this->userRegisterRepository->save($userRegister);

		try {
			MailService::send(
				$userRegister->getEmail(),
				"inscription",
				$_ENV["HOST"] . "/validate?id=" . $userRegister->getId()
			);
		} catch (\Exception $error) {
			$this->userRegisterRepository->delete($userRegister);
			throw new \Exception(
				"Il y a ue un problème lors dde l'envoie du mail."
			);
		}
	}

	public function validate(?string $id)
	{
		if (!$id) {
			throw new \Exception("Missig id.");
		}

		/** @var UserRegister $userRegisterFinded */
		$userRegisterFinded = $this->userRegisterRepository->getById($id);

		if (!$userRegisterFinded) {
			throw new \Exception("User register not found.");
		}

		$now = new \DateTime("now");

		$this->userRegisterRepository->delete($userRegisterFinded);

		if (
			$userRegisterFinded->getCreatedAt()->getTimestamp() +
				self::timeoutRegisterUser <
			$now->getTimestamp()
		) {
			throw new \Exception("User register has expired.");
		}

		return $this->userRepository->create($userRegisterFinded);
	}

	/**
	 * @param array[email: string, password: string] $data
	 * @return User
	 */
	public function login(User $user): User
	{
		// check if user
		$findedUser = $this->userRepository->getByEmail(
			$user->getEmail("email")
		);
		if (!$findedUser) {
			throw new \Exception("Account with email not found");
		}
		// check password
		if (
			!password_verify(
				$user->getPassword("password"),
				$findedUser->getPassword()
			)
		) {
			throw new \Exception("Wrong password");
		}

		return $findedUser;
	}

	public function forgetPassword(User $user)
	{
		$findedUser = $this->userRepository->getByEmail($user->getEmail());

		if (!$findedUser) {
			throw new \Exception("Cette adresse email n'existe pas.");
		}

		$findedForgetPassword = $this->forgetPasswordRepository->getByUser(
			$findedUser
		);
		$now = new \DateTime("now");

		if (
			$findedForgetPassword &&
			$findedForgetPassword->getCreatedAt()->getTimestamp() +
				self::timeoutRegisterUser <
				$now->getTimestamp()
		) {
			$this->forgetPasswordRepository->delete($findedForgetPassword);
			$findedForgetPassword = null;
		}

		if ($findedForgetPassword) {
			throw new \Exception(
				"Un email de récupération à déjà été envoyé a cette adresse."
			);
		}

		$forgetPassword = new ForgetPassword();
		$forgetPassword->setUser($findedUser);
		$this->forgetPasswordRepository->save($forgetPassword);

		try {
			MailService::send(
				$findedUser->getEmail(),
				"récupération de mot de passe",
				$_ENV["HOST"] .
					"/change-password?id=" .
					$forgetPassword->getId()
			);
		} catch (\Exception $error) {
			$this->forgetPasswordRepository->delete($forgetPassword);
			throw new \Exception(
				"Il y a ue un problème lors dde l'envoie du mail."
			);
		}
	}

	public function changePassword(User $user, ForgetPassword $forgetPassword)
	{
		$forgetPassword
			->getUser()
			->setPassword(
				password_hash($user->getPassword(), PASSWORD_DEFAULT)
			);

		$this->forgetPasswordRepository->delete($forgetPassword);
	}
}
