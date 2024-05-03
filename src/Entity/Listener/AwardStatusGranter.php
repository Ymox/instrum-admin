<?php

namespace App\Entity\Listener;

use App\Entity\Award;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AwardStatusGranter
{
    public function grantStatus(Award $award, LifecycleEventArgs $event): void
    {
        if ($status = $award->getAwardType()->getStatusToAward()) {
            $member = $award->getMember();
            foreach ($member->getStatuses() as $currentStatus) {
                if ($currentStatus->getId() == 1) {
                    $member->removeStatus($currentStatus);
                    break;
                }
            }

            if (!$member->getStatuses()->contains($status)) {
                $member->addStatus($status);
            }
        }
    }
}