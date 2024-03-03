<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Toggle
{
	public bool $isChecked = false;
	public string $title;
	public ?string $onClick = null;
	public ?string $name = null;
}
