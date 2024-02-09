<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class AdvantageCard
{
	public string $title;
	public string $description;
	public string $image = "checkbox.png";
}
