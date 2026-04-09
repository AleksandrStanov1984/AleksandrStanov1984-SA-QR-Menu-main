<?php

namespace App\DTO;

class ItemMetaDTO
{
    public bool $isNew;
    public bool $dishOfDay;
    public bool $showImage;
    public int $spicy;
    public array $style;

    public bool $bestseller;

    public function __construct(array $data = [])
    {
        $this->isNew      = (bool)($data['is_new'] ?? false);
        $this->dishOfDay  = (bool)($data['dish_of_day'] ?? false);
        $this->showImage  = (bool)($data['show_image'] ?? true);
        $this->spicy      = (int)($data['spicy'] ?? 0);
        $this->style      = is_array($data['style'] ?? null) ? $data['style'] : [];

        $this->bestseller = (bool)($data['bestseller'] ?? false);
    }

    public static function fromModel($item): self
    {
        return new self(is_array($item->meta) ? $item->meta : []);
    }

    public function apply(array $data): void
    {
        if (array_key_exists('is_new', $data)) {
            $this->isNew = filter_var($data['is_new'], FILTER_VALIDATE_BOOLEAN);
        }

        if (array_key_exists('dish_of_day', $data)) {
            $this->dishOfDay = filter_var($data['dish_of_day'], FILTER_VALIDATE_BOOLEAN);
        }

        if (array_key_exists('show_image', $data)) {
            $this->showImage = filter_var($data['show_image'], FILTER_VALIDATE_BOOLEAN);
        }

        if (array_key_exists('spicy', $data)) {
            $this->spicy = (int)$data['spicy'];
        }

        if (array_key_exists('style', $data)) {
            $this->style = is_array($data['style']) ? $data['style'] : [];
        }

        if (array_key_exists('bestseller', $data)) {
            $this->bestseller = filter_var($data['bestseller'], FILTER_VALIDATE_BOOLEAN);
        }
    }

    public function toArray(): array
    {
        return [
            'is_new'       => $this->isNew,
            'dish_of_day'  => $this->dishOfDay,
            'show_image'   => $this->showImage,
            'spicy'        => $this->spicy,
            'style'        => $this->style,
            'bestseller'   => $this->bestseller,
        ];
    }
}

