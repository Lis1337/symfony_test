<?php

namespace App\Entity;

class Fruit
{
    private string $color;
    private string $size;

    /**
     * @param string $color
     * @param string $size
     */
    public function __construct(
        string $color,
        string $size
    )
    {
        $this->color = $color;
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }
}
