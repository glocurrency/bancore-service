<?php

namespace GloCurrency\Bancore\Enums;

use GloCurrency\MiddlewareBlocks\Enums\ProcessingItemStateCodeEnum as MProcessingItemStateCodeEnum;

enum TransactionStateCodeEnum: string
{
    case LOCAL_UNPROCESSED = 'local_unprocessed';
    case LOCAL_EXCEPTION = 'local_exception';
    case STATE_NOT_ALLOWED = 'state_not_allowed';
    case API_REQUEST_EXCEPTION = 'api_request_exception';
    case RESULT_JSON_INVALID = 'result_json_invalid';
    case NO_ERROR_CODE_PROPERTY = 'no_error_code_property';
    case UNEXPECTED_ERROR_CODE = 'unexpected_error_code';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case API_ERROR = 'api_error';
    case API_TIMEOUT = 'api_timeout';
    case INSUFFICIENT_FUNDS = 'insufficient_funds';
    case DUPLICATE_TRANSACTION = 'duplicate_transaction';
    case TRANSACTION_AMOUNT_INVALID = 'transaction_amount_invalid';
    case SENDER_DETAILS_INVALID = 'sender_details_invalid';
    case SENDER_TRANSFER_LIMIT_EXCEEDED = 'sender_transfer_limit_exceeded';
    case RECIPIENT_BANK_ACCOUNT_INVALID = 'recipient_bank_account_invalid';
    case RECIPIENT_TRANSFER_LIMIT_EXCEEDED = 'recipient_transfer_limit_exceeded';
    case RECIPIENT_DETAILS_INVALID = 'recipient_details_invalid';

    /**
     * Get the ProcessingItem state based on Transaction state.
     */
    public function getProcessingItemStateCode(): MProcessingItemStateCodeEnum
    {
        return match ($this) {
            self::LOCAL_UNPROCESSED => MProcessingItemStateCodeEnum::PENDING,
            self::LOCAL_EXCEPTION => MProcessingItemStateCodeEnum::MANUAL_RECONCILIATION_REQUIRED,
            self::STATE_NOT_ALLOWED => MProcessingItemStateCodeEnum::EXCEPTION,
            self::API_REQUEST_EXCEPTION => MProcessingItemStateCodeEnum::EXCEPTION,
            self::RESULT_JSON_INVALID => MProcessingItemStateCodeEnum::EXCEPTION,
            self::NO_ERROR_CODE_PROPERTY => MProcessingItemStateCodeEnum::EXCEPTION,
            self::UNEXPECTED_ERROR_CODE => MProcessingItemStateCodeEnum::EXCEPTION,
            self::PROCESSING => MProcessingItemStateCodeEnum::PROVIDER_PENDING,
            self::PAID => MProcessingItemStateCodeEnum::PROCESSED,
            self::API_ERROR => MProcessingItemStateCodeEnum::PROVIDER_NOT_ACCEPTING_TRANSACTIONS,
            self::API_TIMEOUT => MProcessingItemStateCodeEnum::PROVIDER_TIMEOUT,
            self::INSUFFICIENT_FUNDS => MProcessingItemStateCodeEnum::PROVIDER_NOT_ACCEPTING_TRANSACTIONS,
            self::DUPLICATE_TRANSACTION => MProcessingItemStateCodeEnum::EXCEPTION,
            self::TRANSACTION_AMOUNT_INVALID => MProcessingItemStateCodeEnum::TRANSACTION_AMOUNT_INVALID,
            self::SENDER_DETAILS_INVALID => MProcessingItemStateCodeEnum::SENDER_DETAILS_INVALID,
            self::SENDER_TRANSFER_LIMIT_EXCEEDED => MProcessingItemStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
            self::RECIPIENT_BANK_ACCOUNT_INVALID => MProcessingItemStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
            self::RECIPIENT_TRANSFER_LIMIT_EXCEEDED => MProcessingItemStateCodeEnum::RECIPIENT_TRANSFER_LIMIT_EXCEEDED,
            self::RECIPIENT_DETAILS_INVALID => MProcessingItemStateCodeEnum::RECIPIENT_DETAILS_INVALID,
        };
    }
}
