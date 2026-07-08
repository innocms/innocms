<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Repositories\Product;

use InnoCMS\Common\Models\Product;
use InnoCMS\Common\Models\Product\Relation;
use InnoCMS\Common\Repositories\BaseRepo;

class RelationRepo extends BaseRepo
{
    /**
     * Handle bidirectional relations between products.
     *
     * @param  Product  $product
     * @param  array  $relationIDs
     * @return void
     */
    public function handleBidirectionalRelations(Product $product, array $relationIDs): void
    {
        // Clear all existing forward relations
        $product->relations()->delete();

        // Clear reverse relations pointing back at this product
        Relation::where('relation_id', $product->id)->delete();

        if (empty($relationIDs)) {
            return;
        }

        // Normalise to positive ints, dedupe, drop empties / non-numeric / self-reference
        $relationIDs = collect($relationIDs)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->reject(fn ($id) => $id === (int) $product->id)
            ->values()
            ->all();

        if (empty($relationIDs)) {
            return;
        }

        // Prepare both forward and reverse relations
        $forwardRelations = [];
        $reverseRelations = [];
        $now              = now();

        foreach ($relationIDs as $id) {
            // Forward relation (product -> related)
            $forwardRelations[] = [
                'product_id'  => $product->id,
                'relation_id' => $id,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            // Reverse relation (related -> product)
            $reverseRelations[] = [
                'product_id'  => $id,
                'relation_id' => $product->id,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        Relation::insert($forwardRelations);
        Relation::insert($reverseRelations);
    }
}
