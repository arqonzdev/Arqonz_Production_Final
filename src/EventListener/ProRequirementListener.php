<?php
// src/EventListener/ProRequirementListener.php

namespace App\EventListener;

use Pimcore\Model\DataObject\ProRequirement;
use Pimcore\Event\Model\DataObjectEvent;
use Carbon\Carbon;
use Pimcore\Model\DataObject\ProRequirementProduct\Listing as ProRequirementProductListing;

class ProRequirementListener
{
    public function onPreUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof ProRequirement) {
            $expireDate = $object->getExpireDate();

            if ($expireDate instanceof Carbon && $expireDate->isPast()) {
                // Unpublish the ProRequirement object
                $object->setPublished(false);
                $object->save();

                // Unpublish all related ProRequirementProduct objects
                $productListing = new ProRequirementProductListing();
                $productListing->setCondition('ProRequirement = ?', [$object->getKey()]);

                foreach ($productListing as $product) {
                    $product->setPublished(false);
                    $product->save();
                }
            }
        }
    }
}
