<?php

namespace App\Exports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersCSVTemplate implements FromArray, WithProperties, WithColumnFormatting, ShouldAutoSize, WithHeadings, WithStyles
{
    use Exportable;

    public $powasID;
    public $userID;
    public $numberOfMembers;

    public function __construct($userID, $powasID, $numberOfMembers)
    {
        $this->userID = $userID;
        $this->powasID = $powasID;
        $this->numberOfMembers = $numberOfMembers;
    }

    public function headings(): array
    {
        return [
            [
                'powas_id',
                'user_id',
                'lastname',
                'firstname',
                'middlename',
                'birthday',
                'birthplace',
                'gender',
                'contact_number',
                'civil_status',
                'address1',
                'barangay',
                'municipality',
                'province',
                'region',
                'present_address',
                'family_members',
                'application_status',
                'meter_number',
                'membership_date',
                'firstfifty',
                'land_owner',
                'member_status',
            ]
        ];
    }

    public function array(): array
    {
        $preset = [];

        for ($i = 1; $i <= $this->numberOfMembers; $i++) {
            // $preset[] = [$this->powasID, $this->userID];
            $preset[] = [$this->powasID, $this->userID];
        }

        return $preset;
    }

    public function properties(): array
    {
        return [
            'title' => $this->powasID,
            'creator' => $this->userID,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'T' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A')->getFont()->setBold(true);
        $sheet->getStyle('B')->getFont()->setBold(true);
        $sheet->getStyle('1')->getFont()->setBold(true);

        $sheet->getStyle('C2:W' . $this->numberOfMembers + 1)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle('C2:W' . $this->numberOfMembers + 1)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color(Color::COLOR_LIGHTYELLOW));

        $sheet->getStyle('A1:W' . $this->numberOfMembers + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $protectSheet = $sheet->getParent()->getActiveSheet()->getProtection();

        $protectSheet->setPassword('jojieandy1825');
        $protectSheet->setSheet(true);
        $protectSheet->setSelectLockedCells(false);
        $protectSheet->setSelectUnlockedCells(false);
        $protectSheet->setInsertColumns(false);
        $protectSheet->setInsertRows(false);
        $protectSheet->setFormatCells(false);
        $protectSheet->setFormatColumns(false);
        $protectSheet->setFormatRows(false);
        $protectSheet->setInsertHyperlinks(false);
        $protectSheet->setDeleteColumns(false);
        $protectSheet->setDeleteRows(false);
        $protectSheet->setSort(false);
        $protectSheet->setAutoFilter(false);
        $protectSheet->setPivotTables(false);

        $generalComment = $sheet->getParent()->getActiveSheet()->getComment('A1');
        $generalComment->setAuthor('Jhay Alabab');
        $authorText = $generalComment->getText()->createTextRun($generalComment->getAuthor() . ': ');
        $authorText->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
        $commentText = $generalComment->getText()->createTextRun("\r\n");
        $commentText = $generalComment->getText()->createTextRun('Please fill out all the information and do not leave any blank cells with yellow background.');
        $commentText->getFont()->setColor(new Color(Color::COLOR_WHITE));
        $commentText = $generalComment->getText()->createTextRun("\r\n");
        $commentText = $generalComment->getText()->createTextRun("\r\n");
        $commentText = $generalComment->getText()->createTextRun('(Hover on me to hide this comment)');
        $commentText->getFont()->setColor(new Color(Color::COLOR_WHITE));
        $generalComment->setWidth(450);
        $generalComment->setHeight(75);
        $generalComment->setVisible(true);
        $generalComment->setFillColor(new Color(Color::COLOR_BLACK));
        $generalComment->getAuthor();

        for ($i = 2; $i <= $this->numberOfMembers + 1; $i++) {
            $validation = $sheet->getParent()->getActiveSheet()->getCell('H' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_WARNING);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Gender Input Error!');
            $validation->setError('Value is not on the list!');
            $validation->setPromptTitle('Gender Input');
            $validation->setPrompt('Please pick a value from the dropdown list.');
            $validation->setFormula1('"MALE,FEMALE"');
        }

        for ($i = 2; $i <= $this->numberOfMembers + 1; $i++) {
            $validation = $sheet->getParent()->getActiveSheet()->getCell('J' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_WARNING);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Civil Status Input Error!');
            $validation->setError('Value is not on the list!');
            $validation->setPromptTitle('Civil Status Input');
            $validation->setPrompt("Please pick a value from the dropdown list.\nTake note that 'WIDOW' is for 'FEMALE', and 'WIDOWER' is for 'MALE'");
            $validation->setFormula1('"SINGLE,MARRIED,WIDOW,WIDOWER,LEGALLY SEPARATED"');
        }

        for ($i = 2; $i <= $this->numberOfMembers + 1; $i++) {
            $validation = $sheet->getParent()->getActiveSheet()->getCell('R' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_WARNING);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Member Status Input Error!');
            $validation->setError('Value is not on the list!');
            $validation->setPromptTitle('Member Status Input');
            $validation->setPrompt('Please pick a value from the dropdown list.');
            $validation->setFormula1('"APPROVED"');
        }

        for ($i = 2; $i <= $this->numberOfMembers + 1; $i++) {
            $validation = $sheet->getParent()->getActiveSheet()->getCell('U' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_WARNING);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('First 50 Input Error!');
            $validation->setError('Value is not on the list!');
            $validation->setPromptTitle('First 50 Input');
            $validation->setPrompt('Please pick a value from the dropdown list.');
            $validation->setFormula1('"Y,N"');
        }

        for ($i = 2; $i <= $this->numberOfMembers + 1; $i++) {
            $validation = $sheet->getParent()->getActiveSheet()->getCell('V' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_WARNING);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Land Owner Input Error!');
            $validation->setError('Value is not on the list!');
            $validation->setPromptTitle('Land Owner Input');
            $validation->setPrompt('Please pick a value from the dropdown list.');
            $validation->setFormula1('"Y,N"');
        }

        for ($i = 2; $i <= $this->numberOfMembers + 1; $i++) {
            $validation = $sheet->getParent()->getActiveSheet()->getCell('W' . $i)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_WARNING);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Member Status Input Error!');
            $validation->setError('Value is not on the list!');
            $validation->setPromptTitle('Member Status Input');
            $validation->setPrompt('Please pick a value from the dropdown list.');
            $validation->setFormula1('"ACTIVE,LOCKED,DISCONNECTED"');
        }
    }
}
