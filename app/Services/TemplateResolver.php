<?php


namespace App\Services;
class TemplateResolver
{
    public static function resolve($templateKey): string
    {
        return match ($templateKey) {

            default => 'public.templates.united.layout',
        };
    }
}
