<?php

namespace App\Exception;

class BustiException extends \Exception
{
    public const ERROR_GENERIC            = 0;
    public const ERROR_DATA_REQUIRED      = 1;
    public const ERROR_FORMAT_VALIDATION  = 2;
    public const ERROR_NOT_UNIQUE         = 3;
    public const ERROR_PASSWORD_NOT_VALID = 4;

    /** @var array */
    private const MESSAGES = [
        self::ERROR_GENERIC            => 'Errore generico.',
        self::ERROR_DATA_REQUIRED      => 'Dato obbligatorio non fornito.',
        self::ERROR_FORMAT_VALIDATION  => 'Formato del dato non valido.',
        self::ERROR_NOT_UNIQUE         => 'Dato già presente.',
        self::ERROR_PASSWORD_NOT_VALID => 'Password non valida.',
    ];

    protected $code = self::ERROR_GENERIC;

    /**
     * RiverestException constructor.
     *
     * @param null|string $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct();
        $this->message = $message ?? self::getMessageFromCode($this->code);
    }

    /**
     * @param int $code
     *
     * @return string
     */
    public static function getMessageFromCode($code): string
    {
        if (isset(self::MESSAGES[$code])) {
            return self::MESSAGES[$code];
        }

        return self::MESSAGES[self::ERROR_GENERIC];
    }
}
