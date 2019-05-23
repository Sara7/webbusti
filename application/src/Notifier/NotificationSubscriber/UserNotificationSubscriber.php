<?php

namespace App\Notifier\NotificationSubscriber;

use App\Entity\User;
use App\Events\AddressEvent;
use App\Events\AddressEvents;
use App\Events\OrderEvent;
use App\Events\OrderEvents;
use App\Events\UserEvent;
use App\Events\UserEvents;
use App\Notifier\UserNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserNotificationSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserNotifier
     */
    private $notifier;

    /**
     * NotifyUserOnEvents constructor.
     *
     * @param UserNotifier $notifier
     */
    public function __construct(UserNotifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserEvents::CREATED          => 'notifyOnUserCreated',
            UserEvents::RECOVER_PASSWORD => 'notifyOnPasswordForgot',
            AddressEvents::CREATED       => 'notifyOnAddressCreated',
            OrderEvents::CREATED         => 'notifyOnOrderCreated',
            OrderEvents::DELETED         => 'notifyOnOrderCanceled',
            OrderEvents::SHIPPED         => 'notifyOnOrderShipped',
        ];
    }

    /**
     * @param UserEvent $event
     */
    public function notifyOnUserCreated(UserEvent $event): void
    {
        $user = $event->getUser();

        $this->notifier->notify($user, 'user_created', 'Benvenuto su Bustistore!');
    }

    /**
     * @param UserEvent $event
     */
    public function notifyOnPasswordForgot(UserEvent $event): void
    {
        $user = $event->getUser();

        $this->notifier->notify($user, 'user_password_forgot', 'Recupero password Bustistore');
    }

    /**
     * @param AddressEvent $event
     */
    public function notifyOnAddressCreated(AddressEvent $event): void
    {
        $address = $event->getAddress();

        $this->notifier->notify(
            $address->getUser(),
            'user_address_added',
            'Nuovo indirizzo di spedizione registrato',
            [
                'address' => $address,
            ]
        );
    }

    /**
     * @param OrderEvent $event
     */
    public function notifyOnOrderCreated(OrderEvent $event): void
    {
        $order = $event->getOrder();
        /** @var User $user */
        $user = $order->getUser();

        $title = sprintf('Conferma Ordine id %s', $order->getId());

        $this->notifier->notify(
            $user,
            'user_order_confirmed',
            $title,
            [
                'order' => $order,
            ]
        );
    }

    /**
     * @param OrderEvent $event
     */
    public function notifyOnOrderCanceled(OrderEvent $event): void
    {
        $order = $event->getOrder();
        /** @var User $user */
        $user = $order->getUser();

        $title = sprintf('Cancellazione Ordine id %s', $order->getId());

        $this->notifier->notify(
            $user,
            'user_order_canceled',
            $title,
            [
                'order' => $order,
            ]
        );
    }

    /**
     * @param OrderEvent $event
     */
    public function notifyOnOrderShipped(OrderEvent $event): void
    {
        $order = $event->getOrder();
        /** @var User $user */
        $user = $order->getUser();

        $this->notifier->notify(
            $user,
            'user_order_shipped',
            'Abbiamo Spedito la tua Merce!',
            [
                'order' => $order,
            ]
        );
    }
}
