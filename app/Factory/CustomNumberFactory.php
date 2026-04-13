<?php

namespace App\Factory;

use App\Models\IssuedReceipts;
use App\Models\Powas;
use App\Models\Transactions;
use App\Models\Vouchers;
use Carbon\Carbon;

class CustomNumberFactory
{
    public static function receipt($powasID, $recordDate)
    {
        $today = Carbon::parse($recordDate);
        $datePart = $today->format('Ym');

        // Optimization: Use SQL MAX to find the highest existing sequence derived from the receipt number string
        // Assumes receipt number format is YYYYMM + Sequence (e.g., 2023120001) where the suffix is variable length
        // We look for numbers starting with the datePart
        $maxReceiptNumber = IssuedReceipts::where('powas_id', $powasID)
            ->where('receipt_number', 'like', $datePart . '%')
            ->max('receipt_number');

        $sequence = 0;
        if ($maxReceiptNumber) {
            // Remove the date prefix to get the sequence
             $sequence = intval(substr($maxReceiptNumber, strlen($datePart)));
        }

        $nextSequence = $sequence + 1;

        // Maintain the original padding logic
        if ($nextSequence < 10) {
            return $datePart . '000' . $nextSequence;
        } elseif ($nextSequence < 100) {
            return $datePart . '00' . $nextSequence;
        } elseif ($nextSequence < 1000) {
            return $datePart . '0' . $nextSequence;
        } else {
            return $datePart . $nextSequence;
        }
    }

    public static function voucher($powasID, $recordDate)
    {
        $today = Carbon::parse($recordDate);
        $datePart = $today->format('y-m');

        // Optimization: Use SQL MAX on the substring to get the highest sequence
        // Format: YY-MM-SEQUENCE (e.g. 24-01-0001)
        
        $maxSequence = Vouchers::where('powas_id', $powasID)
            ->whereYear('voucher_date', $today->year)
            ->whereMonth('voucher_date', $today->month)
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(voucher_number, "-", -1) AS UNSIGNED)) as max_seq')
            ->value('max_seq');

        $nextSequence = ($maxSequence ?? 0) + 1;

        if ($nextSequence < 10) {
            return $datePart . '-000' . $nextSequence;
        } elseif ($nextSequence < 100) {
            return $datePart . '-00' . $nextSequence;
        } elseif ($nextSequence < 1000) {
            return $datePart . '-0' . $nextSequence;
        } else {
            return $datePart . '-' . $nextSequence;
        }
    }

    public static function journalEntryNumber($powasID, $recordDate)
    {
        $today = Carbon::parse($recordDate);
        $datePart = $today->format('y-m');
        
        // Optimization: Direct SQL MAX calculation
        // Format: YY-MM-NNNN (e.g. 24-01-0052)
        
        $maxSequence = Transactions::where('powas_id', $powasID)
            ->whereYear('transaction_date', $today->year)
            ->whereMonth('transaction_date', $today->month)
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(journal_entry_number, "-", -1) AS UNSIGNED)) as max_seq')
            ->value('max_seq');

        $nextSequence = ($maxSequence ?? 0) + 1;

        // Format with leading zeros
        if ($nextSequence < 10) {
            return $datePart . '-000' . $nextSequence;
        } elseif ($nextSequence < 100) {
            return $datePart . '-00' . $nextSequence;
        } elseif ($nextSequence < 1000) {
            return $datePart . '-0' . $nextSequence;
        } else {
            return $datePart . '-' . $nextSequence;
        }
    }

    public static function powasID($province, $municipality, $barangay)
    {
        $instance = new self();
        $str1 = $instance->generateAbbreviation($province);
        $str2 = $instance->generateAbbreviation($municipality);
        $str3 = $instance->generateAbbreviation($barangay);
        
        // This format is STR-STR-STR-NUM (e.g. NEC-SJC-PIN-004)
        // Similar MAX logic should be applied to avoid count() issues
        $prefix = $str1 . '-' . $str2 . '-' . $str3 . '-';
        
        $maxPowas = Powas::where('province', $province)
            ->where('municipality', $municipality)
            ->where('barangay', $barangay)
            ->where('powas_id', 'like', $prefix . '%')
            ->max('powas_id');
            
        $sequence = 0;
        if ($maxPowas) {
             $sequence = intval(substr($maxPowas, strlen($prefix)));
        }
        
        $nextSequence = $sequence + 1;

        if ($nextSequence < 10) {
            $str4 = '00' . $nextSequence;
        } elseif ($nextSequence < 100) {
            $str4 = '0' . $nextSequence;
        } else {
            $str4 = $nextSequence;
        }

        return $prefix . $str4;
    }

    private static function generateAbbreviation($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        $words = explode(' ', $string);

        if (count($words) == 1) {
            $abbreviation = strtoupper(substr($words[0], 0, 3));
        } elseif (count($words) == 2) {
            $abbreviation = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 2));
        } else {
            $abbreviation = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1));
        }

        return $abbreviation;
    }

    public static function getRandomID()
    {
        return rand(1000000000000000000, 9223372036854775807);
    }
}
