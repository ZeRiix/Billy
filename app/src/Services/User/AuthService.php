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
			throw new \Exception("Cette adresse email est déjà utilisée.");
		}

		$userRegisterFinded = $this->userRegisterRepository->getByEmail($userRegister->getEmail());

		if (
			$userRegisterFinded &&
			$userRegisterFinded->getCreatedAt()->getTimestamp() + self::timeoutRegisterUser <
				(new \DateTime("now"))->getTimestamp()
		) {
			$this->userRegisterRepository->delete($userRegisterFinded);
			$userRegisterFinded = null;
		}

		if ($userRegisterFinded) {
			throw new \Exception("Un email à déjà été envoyé à cette adresse.");
		}

		$userRegister->setPassword(password_hash($userRegister->getPassword(), PASSWORD_DEFAULT));
		$this->userRegisterRepository->save($userRegister);

		try {
			MailService::send(
				$userRegister->getEmail(),
				"inscription",
				$_ENV["HOST"] . "/validate?id=" . $userRegister->getId()
			);
		} catch (\Exception $error) {
			$this->userRegisterRepository->delete($userRegister);
			throw new \Exception("Il y a eu un problème lors de l'envoie de l'email.");
		}
	}

	public function validate(?string $id)
	{
		if (!$id) {
			throw new \Exception("Missing id");
		}

		/** @var UserRegister $userRegisterFinded */
		$userRegisterFinded = $this->userRegisterRepository->getById($id);

		if (!$userRegisterFinded) {
			throw new \Exception("User register not found");
		}

		$this->userRegisterRepository->delete($userRegisterFinded);

		if (
			$userRegisterFinded->getCreatedAt()->getTimestamp() + self::timeoutRegisterUser <
			(new \DateTime("now"))->getTimestamp()
		) {
			throw new \Exception("User register has expired");
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
		$findedUser = $this->userRepository->getByEmail($user->getEmail("email"));
		if (!$findedUser) {
			throw new \Exception("L'email ou le mot de passe n'est pas valide.");
		}
		// check password
		if (!password_verify($user->getPassword("password"), $findedUser->getPassword())) {
			throw new \Exception("L'email ou le mot de passe n'est pas valide.");
		}

		return $findedUser;
	}

	public function forgetPassword(User $user)
	{
		$findedUser = $this->userRepository->getByEmail($user->getEmail());

		if (!$findedUser) {
			throw new \Exception("Cette adresse email n'existe pas.");
		}

		$findedForgetPassword = $this->forgetPasswordRepository->getByUser($findedUser);

		if (
			$findedForgetPassword &&
			$findedForgetPassword->getCreatedAt()->getTimestamp() + self::timeoutRegisterUser <
				(new \DateTime("now"))->getTimestamp()
		) {
			$this->forgetPasswordRepository->delete($findedForgetPassword);
			$findedForgetPassword = null;
		}

		if ($findedForgetPassword) {
			throw new \Exception("Un email de récupération a déjà été envoyé à cette adresse.");
		}

		$forgetPassword = new ForgetPassword();
		$forgetPassword->setUser($findedUser);
		$this->forgetPasswordRepository->save($forgetPassword);

		try {
			MailService::send(
				$findedUser->getEmail(),
				"récupération de mot de passe",
				$_ENV["HOST"] . "/change-password?id=" . $forgetPassword->getId()
			);
		} catch (\Exception $error) {
			$this->forgetPasswordRepository->delete($forgetPassword);
			throw new \Exception("Il y a eu un problème lors de l'envoie de l'email.");
		}
	}

	public function changePassword(User $user, ForgetPassword $forgetPassword)
	{
		$this->forgetPasswordRepository->delete($forgetPassword);

		if (
			$forgetPassword &&
			$forgetPassword->getCreatedAt()->getTimestamp() + self::timeoutRegisterUser <
				(new \DateTime("now"))->getTimestamp()
		) {
			throw new \Exception("La demande de changement de mots de passe a expiré.");
		}

		$userForgetPassword = $forgetPassword->getUser();

		$userForgetPassword->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

		$this->forgetPasswordRepository->save($userForgetPassword);
	}
}
