<?php

namespace App\Services\SectionPositionService;

use App\Models\Section;
use Illuminate\Support\Collection;

class SectionPositionService
{
    public function apply(Section $section, string $mode, ?int $targetId = null): void
    {
        if ($mode === 'keep') {
            return;
        }

        $siblings = Section::where('restaurant_id', $section->restaurant_id)
            ->where('parent_id', $section->parent_id)
            ->orderBy('sort_order')
            ->get();

        $list = $siblings
            ->reject(fn ($i) => $i->id === $section->id)
            ->values();

        switch ($mode) {

            case 'start':
                $list->prepend($section);
                break;

            case 'end':
                $list->push($section);
                break;

            case 'before':
            case 'after':

                if (!$targetId) {
                    $list->push($section);
                    break;
                }

                $target = Section::where('id', $targetId)
                    ->where('restaurant_id', $section->restaurant_id)
                    ->where('parent_id', $section->parent_id)
                    ->first();

                if (!$target) {
                    $list->push($section);
                    break;
                }

                $index = $list->search(fn ($i) => $i->id === $targetId);

                if ($index === false) {
                    $list->push($section);
                    break;
                }

                if ($mode === 'after') {
                    $index++;
                }

                $list->splice($index, 0, [$section]);

                break;

            default:
                $list->push($section);
        }

        $this->reorder($list);
    }

    private function reorder(Collection $list): void
    {
        foreach ($list as $index => $item) {

            $newOrder = $index + 1;

            if ((int)$item->sort_order !== $newOrder) {
                $item->update([
                    'sort_order' => $newOrder
                ]);
            }
        }
    }
}
