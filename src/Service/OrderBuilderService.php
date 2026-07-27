<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Menu;

class OrderBuilderService
{
    public function fillFromUser(Order $order, User $user): void
    {
        $order
            ->setUser($user)

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

    public function buildRequestedDate(\DateTimeInterface $serviceDate, string $requestedTime): \DateTimeImmutable
    {
        return new \DateTimeImmutable(
            $serviceDate->format('Y-m-d') . ' ' . $requestedTime
        );
    }
}