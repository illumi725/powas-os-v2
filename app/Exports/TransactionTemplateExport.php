<?php

namespace App\Exports;

use App\Models\ChartOfAccounts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TransactionTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new TransactionDataSheet(),
            new ReferenceDataSheet(),
        ];
    }
}

class TransactionDataSheet implements WithHeadings, WithTitle, WithEvents
{
    public function title(): string
    {
        return 'Transactions';
    }

    public function headings(): array
    {
        return [
            'Transaction Type',
            'Account Name',
            'Date (YYYY-MM-DD)',
            'Amount',
            'Description',
            'Received From / Paid To'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Transaction Type Dropdown (Column A)
                $validation = $event->sheet->getCell('A2')->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a value from the drop-down list.');
                $validation->setFormula1('"Receipts,Payments,Expenses"');

                // Clone validation to rows 3-1000
                for ($i = 3; $i <= 1000; $i++) {
                    $event->sheet->getCell("A$i")->setDataValidation(clone $validation);
                }

                // Account Name Dependent Dropdown (Column B)
                // Reverted to INDIRECT with absolute references and fully qualified Named Ranges
                
                $validation2 = $event->sheet->getCell('B2')->getDataValidation();
                $validation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                $validation2->setAllowBlank(false);
                $validation2->setShowInputMessage(true);
                $validation2->setShowErrorMessage(true);
                $validation2->setShowDropDown(true);
                // Use INDIRECT with absolute column reference ($A) to ensure it tracks correctly
                $validation2->setFormula1('=INDIRECT($A2)');

                for ($i = 3; $i <= 1000; $i++) {
                    $validation2 = $event->sheet->getCell("B$i")->getDataValidation();
                    $validation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
                    $validation2->setAllowBlank(false);
                    $validation2->setShowInputMessage(true);
                    $validation2->setShowErrorMessage(true);
                    $validation2->setShowDropDown(true);
                    // Absolute column $A, relative row $i
                    $validation2->setFormula1('=INDIRECT($A' . $i . ')');
                }
            },
        ];
    }
}

class ReferenceDataSheet implements WithTitle, WithEvents
{
    public function title(): string
    {
        return 'ReferenceData';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Get Accounts
                // Receipts: 202, 203, 204, 205, 206, 209, 304, 406, 408
                $receiptsAccounts = ChartOfAccounts::whereIn('account_number', [202, 203, 204, 205, 206, 209, 304, 406, 408])->pluck('account_name')->toArray();
                
                // Payments: 103, 201, 202, 203, 204, 205, 206, 208, 209, 303, 304, 305
                $paymentsAccounts = ChartOfAccounts::whereIn('account_number', [103, 201, 202, 203, 204, 205, 206, 208, 209, 303, 304, 305])->pluck('account_name')->toArray();
                
                // Expenses: 501-512
                $expensesAccounts = ChartOfAccounts::whereBetween('account_number', [501, 512])->pluck('account_name')->toArray();

                // Write to sheet
                $sheet = $event->sheet->getDelegate();
                
                // Column A: Receipts
                $sheet->setCellValue('A1', 'Receipts List');
                $row = 2;
                foreach ($receiptsAccounts as $acc) {
                    $sheet->setCellValue('A' . $row, $acc);
                    $row++;
                }
                $lastRowA = $row - 1;

                // Column B: Payments
                $sheet->setCellValue('B1', 'Payments List');
                $row = 2;
                foreach ($paymentsAccounts as $acc) {
                    $sheet->setCellValue('B' . $row, $acc);
                    $row++;
                }
                $lastRowB = $row - 1;

                // Column C: Expenses
                $sheet->setCellValue('C1', 'Expenses List');
                $row = 2;
                foreach ($expensesAccounts as $acc) {
                    $sheet->setCellValue('C' . $row, $acc);
                    $row++;
                }
                $lastRowC = $row - 1;

                // Define Named Ranges using the workbook
                // IMPORTANTE: Explicitly include Sheet Name and Absolute References
                // This ensures compatibility with LibreOffice Calc which can be picky about scopes
                $workbook = $sheet->getParent();
                $title = $sheet->getTitle(); // 'ReferenceData'
                
                // Receipts
                $workbook->addNamedRange(
                    new \PhpOffice\PhpSpreadsheet\NamedRange('Receipts', $sheet, "{$title}!\$A\$2:\$A\$" . $lastRowA)
                );
                
                // Payments
                $workbook->addNamedRange(
                    new \PhpOffice\PhpSpreadsheet\NamedRange('Payments', $sheet, "{$title}!\$B\$2:\$B\$" . $lastRowB)
                );
                
                // Expenses
                $workbook->addNamedRange(
                    new \PhpOffice\PhpSpreadsheet\NamedRange('Expenses', $sheet, "{$title}!\$C\$2:\$C\$" . $lastRowC)
                );

                // Hide the sheet
                $sheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
            },
        ];
    }
}
