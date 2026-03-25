<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\MenuPlan;
use App\ViewModels\PublicMenu\MenuViewModel;

class MenuViewModelTest extends TestCase
{
    public function test_plan_features_applied()
    {
        $plan = MenuPlan::factory()->make([
            'features' => [
                'images' => false,
                'item_modal' => false,
            ],
        ]);

        $restaurant = Restaurant::factory()->make([
            'plan' => $plan,
        ]);

        $vm = new MenuViewModel($restaurant, 'de');

        $this->assertFalse($vm->showImages);
        $this->assertFalse($vm->showItemModal);
    }
}
