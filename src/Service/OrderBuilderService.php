<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Menu;
use App\Constant\AppConstant;

class OrderBuilderService
{
    public function fillFromUser(Order $order, User $user): void
    {
        $order
            ->setCustomerFirstNameAtOrder($user->getFirstName())
            ->setCustomerLastNameAtOrder($user->getLastName())
            ->setCustomerEmailAtOrder($user->getEmail())
            ->setCustomerPhoneAtOrder($user->getPhoneNumber())
            ->setServiceAddress($user->getAddress())
            ->setServiceAddressComplement($user->getAddressComplement())
            ->setServiceZipCode($user->getZipCode())
            ->setServiceCity($user->getCity());
    }

    public function fillFromMenu(Order $order, Menu $menu): void
    {
        $order
            ->setMenu($menu)
            ->setMenuTitleAtOrder($menu->getTitle())
            ->setMenuDescriptionAtOrder($menu->getDescription())
            ->setPricePerPersonAtOrder($menu->getPricePerPerson())
            ->setStarterTitleAtOrder($menu->getStarter()->getTitle())
            ->setMainCourseTitleAtOrder($menu->getMainCourse()->getTitle())
            ->setDessertTitleAtOrder($menu->getDessert()->getTitle())
            ->setAllergensAtOrder($menu->getAllergensAsString());
    }

    public function buildRequestedDate(\DateTimeInterface $serviceDate, ?\DateTimeInterface $requestedTime): \DateTimeImmutable
    {
        $time = $requestedTime
            ? $requestedTime->format('H:i')
            : AppConstant::DEFAULT_REQUESTED_TIME;

        return new \DateTimeImmutable($serviceDate->format('Y-m-d') . ' ' . $time);
    }
}