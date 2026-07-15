<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BlocksCategories;
use App\Models\Block;

class RegressionTest extends TestCase
{
    public function test_offers()
    {
        $response1 = $this->get('/api/ru/blocks/categories/offers/internet-katalog-1');
        $response1->assertStatus(200);
        $data1 = $response1->json();
        
        $response2 = $this->get('/api/ru/blocks/categories/offers/redstar-collaboration-formats');
        $response2->assertStatus(200);
        $data2 = $response2->json();

        dump("DATA 1 (internet-katalog-1) COUNT: " . count($data1['items'] ?? []));
        dump("DATA 2 (redstar-collaboration-formats) COUNT: " . count($data2['items'] ?? []));
        
        // Let's also check DB directly
        $cat2 = BlocksCategories::where('key', 'redstar-collaboration-formats')->first();
        if ($cat2) {
            $items = \App\Models\BlockItem::where('category_id', $cat2->id)->get();
            dump("DB ITEMS FOR redstar-collaboration-formats: " . $items->count());
            dump("DB ITEM block_ids: " . $items->pluck('block_id')->implode(', '));
        }

        $this->assertTrue(true);
    }
}
