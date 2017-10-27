<?php

namespace Dayuse\Test\Istorija\EventSourcing\Fixtures;

use CosplayIt\ESD\EventSourcing\EventStoreRepository;

class MemberRepository
{
    private $eventStoreRepository;

    public function __construct(EventStoreRepository $eventStoreRepository)
    {
        $this->eventStoreRepository = $eventStoreRepository;
    }

    /**
     * @param Member $member
     */
    public function add(Member $member)
    {
        $this->eventStoreRepository->add($member, $member->getMemberId());
    }

    /**
     * @param MemberId $memberId
     * @return Member
     */
    public function get(MemberId $memberId)
    {
        return $this->eventStoreRepository->get(Member::class, $memberId);
    }
}
