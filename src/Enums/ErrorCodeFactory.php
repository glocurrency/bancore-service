<?php

namespace GloCurrency\Bancore\Enums;

use BrokeYourBike\Bancore\Enums\ErrorCodeEnum;

class ErrorCodeFactory
{
    public static function getTransactionStateCode(ErrorCodeEnum $errorCode): TransactionStateCodeEnum
    {
        return match ($errorCode) {
            ErrorCodeEnum::SUCCESS => TransactionStateCodeEnum::PAID,
            ErrorCodeEnum::PENDING => TransactionStateCodeEnum::PROCESSING,
            ErrorCodeEnum::DUPLICATE_SESSION_ID => TransactionStateCodeEnum::DUPLICATE_TRANSACTION,
            ErrorCodeEnum::INVALID_SESSION_ID => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::ORDER_PARAMS_MISMATCH => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::ORDER_EXPIRED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::ORDER_FAILED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::POSSIBLE_DUPLICATE_REQUEST => TransactionStateCodeEnum::DUPLICATE_TRANSACTION,
            ErrorCodeEnum::REMIT_FAILED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::SENDER_LIMIT_EXCEEDED => TransactionStateCodeEnum::SENDER_TRANSFER_LIMIT_EXCEEDED,
            ErrorCodeEnum::RECIPIENT_LIMIT_EXCEEDED => TransactionStateCodeEnum::RECIPIENT_TRANSFER_LIMIT_EXCEEDED,
            ErrorCodeEnum::SENDER_AMOUNT_TOO_SMALL => TransactionStateCodeEnum::TRANSACTION_AMOUNT_INVALID,
            ErrorCodeEnum::SENDER_AMOUNT_TOO_BIG => TransactionStateCodeEnum::TRANSACTION_AMOUNT_INVALID,
            ErrorCodeEnum::SENDER_KYC_FAILED => TransactionStateCodeEnum::SENDER_DETAILS_INVALID,
            ErrorCodeEnum::RECIPIENT_KYC_FAILED => TransactionStateCodeEnum::RECIPIENT_DETAILS_INVALID,
            ErrorCodeEnum::INVALID_ACCOUNT => TransactionStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
            ErrorCodeEnum::PROVIDER_NOT_REACHABLE => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INVALID_AMOUNT => TransactionStateCodeEnum::TRANSACTION_AMOUNT_INVALID,
            ErrorCodeEnum::UNABLE_TO_GET_FOREX_RATE => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INVALID_IDENTIFIER => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::AUTH_FAILED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::MERCHANT_VALIDATION_FAILED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INACTIVE_MERCHANT_STATUS => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::SENDER_CURRENCY_NOT_ENABLED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::MISSING_REQUEST_PARAMS => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INVALID_ENCRYPTION_KEY => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INSUFFICIENT_FUNDS => TransactionStateCodeEnum::INSUFFICIENT_FUNDS,
            ErrorCodeEnum::INVALID_CARD_NUMBER => TransactionStateCodeEnum::RECIPIENT_DETAILS_INVALID,
            ErrorCodeEnum::BLOCKED_CARD => TransactionStateCodeEnum::RECIPIENT_DETAILS_INVALID,
            ErrorCodeEnum::BLOCKED_MOBILE_OR_BANK_ACCOUNT => TransactionStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
            ErrorCodeEnum::INVALID_PIN => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INVALID_CARD_TOKEN => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::FAILED_ATTEMPTS_EXCEEDED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INVALID_MERCHANT_IP => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INVALID_FEE => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::MERCHANT_DAILY_LIMIT_EXCEEDED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::MERCHANT_WEEKLY_LIMIT_EXCEEDED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::MERCHANT_MONTHLY_LIMIT_EXCEEDED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::QUOTA_NOT_FOUND => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::QUOTA_INVALID => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::SERVICE_NOT_CONFIGURED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::SENDER_DAILY_LIMIT_EXCEEDED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::NO_CONFIGURED_WALLET => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::RECIPINT_BANK_NOT_AVAILABLE => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::UNKNOWN_STATUS => TransactionStateCodeEnum::API_TIMEOUT,
            ErrorCodeEnum::TRANSFER_LIMIT_EXCEEDED_AT_PARTNER_INSTITUTION => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::SYSTEM_MALFUNCTION_AT_PARTNER_INSTITUTION => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::PARTNER_NOT_AVAILABLE => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::APPLICATION_ERROR => TransactionStateCodeEnum::API_ERROR,
        };
    }
}
