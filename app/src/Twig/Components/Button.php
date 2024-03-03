<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button
{
	public string $style = "full";
	public string $value;
	public string $href;
	public bool $blank = false;
}
