<?php

declare(strict_types=1);

namespace App;

final class Events
{
    /**
     * @Event("App\Event\DebitEvent")
     */
    public const DEBIT = 'app.debit';

    /**
     * @Event("App\Event\CreditEvent")
     */
    public const CREDIT = 'app.credit';

    /**
     * @Event("App\Event\AuthEvent")
     */
    public const AUTH = 'app.auth';

    /**
     * @Event("App\Event\VoidEvent")
     */
    public const VOID = 'app.void';

    /**
     * @Event("App\Event\CaptureEvent")
     */
    public const CAPTURE = 'app.capture';

    /**
     * @Event("App\Event\TransferEvent")
     */
    public const TRANSFER = 'app.transfer';
}
