<?php

namespace App\Services;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class SchemaService
{
	public static function validateSchema(
		array $data,
		string $schemafile
	): array {
		$tmp = explode("#", $schemafile);
		$schemaName = isset($tmp[1])
			? $tmp[1]
			: array_reverse(explode("/", $schemafile))[0]; // "test/login" -> "test, login" -> "login, test" -> "login"

		$schemafile = $tmp[0];

		$schema = self::getSchema($schemaName, self::readYml($schemafile));

		// Load constraints based on the schema
		$constraints = self::loadConstraints($data, $schema);

		// Validate input data
		$validator = Validation::createValidator();
		foreach ($constraints as $fieldName => $fieldValue) {
			if ($fieldValue !== null) {
				$fieldConstraints = $fieldValue;

				foreach ($fieldConstraints as $constraint) {
					// Valide le champ avec la contrainte correspondante
					$violations = $validator->validate($data[$fieldName], [
						$constraint,
					]);

					// Traitez les violations si nécessaire
					if ($violations->count() > 0) {
						throw new \Exception($violations, 422);
					}
				}
			} else {
				$data[$fieldName] = null;
			}
		}
		return $data;
	}

	public static function readYml(string $schemaName): mixed
	{
		return Yaml::parseFile(
			__DIR__ . "/../Validator/" . $schemaName . ".yml"
		);
	}

	public static function getSchema(string $schemaName, mixed $schema): array
	{
		return $schema[$schemaName] ?? [];
	}

	private static function loadConstraints(
		array $inputData,
		array $schema
	): array {
		$constraints = [];

		foreach ($schema as $fieldName => $fieldConstraints) {
			foreach (
				$fieldConstraints
				as $constraintName => $constraintOptions
			) {
				if ($constraintName === "optional") {
					if (!isset($inputData[$fieldName])) {
						$constraints[$fieldName] = null;
						continue 2;
					} else {
						continue;
					}
				}

				$constraints[$fieldName][] = self::createConstraint(
					$fieldName,
					$constraintName,
					$constraintOptions,
					$inputData
				);
			}
		}

		return $constraints;
	}

	private static function createConstraint(
		$fieldName,
		$constraintName,
		$options,
		$inputData
	) {
		// add constraints if required
		switch ($constraintName) {
			case "NotBlank":
				return new Assert\NotBlank([
					"message" => "$fieldName cannot be blank",
				]);
			case "NotNull":
				return new Assert\NotNull([
					"message" => "$fieldName cannot be null",
				]);
			case "Length":
				return new Assert\Length([
					"min" => $options["min"] ?? null,
					"max" => $options["max"] ?? null,
					"minMessage" => "$fieldName should have at least {{ limit }} characters",
					"maxMessage" => "$fieldName should have at most {{ limit }} characters",
				]);
			case "Email":
				return new Assert\Email([
					"message" => "$fieldName should be a valid email address",
				]);
			case "Choice":
				return new Assert\Choice([
					"choices" => $options["choices"] ?? [],
					"message" => "$fieldName is not a valid choice",
				]);
			case "GreaterThan":
				return new Assert\GreaterThan([
					"value" => $options["value"] ?? null,
					"message" => "$fieldName should be greater than {{ compared_value }}",
				]);
			case "LessThan":
				return new Assert\LessThan([
					"value" => $options["value"] ?? null,
					"message" => "$fieldName should be less than {{ compared_value }}",
				]);
			case "Regex":
				return new Assert\Regex([
					"pattern" => $options["pattern"] ?? "",
					"message" => "$fieldName does not match the required pattern",
				]);
			case "Url":
				return new Assert\Url([
					"message" => "$fieldName should be a valid URL",
				]);
			case "EqualTo":
				return new Assert\EqualTo([
					"value" => $inputData[$options["value"]] ?? null,
					"message" => "$fieldName should be equal to {{ compared_value }}",
				]);
			default:
				throw new \InvalidArgumentException(
					"Unsupported constraint: $constraintName"
				);
		}
	}
}
