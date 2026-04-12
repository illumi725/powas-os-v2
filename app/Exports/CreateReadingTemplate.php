<?php

namespace App\Exports;

use App\Models\PowasMembers;
use App\Models\Readings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CreateReadingTemplate implements FromArray, WithProperties, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles
{
    use Exportable;

    public $powasID;
    public $userID;
    public $memberCount;
    public $toLock = [];

    public function __construct($userID, $powasID)
    {
        $this->userID = $userID;
        $this->powasID = $powasID;
        $this->memberCount = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->where('powas_members.member_status', 'ACTIVE')
            ->get()->count();
    }

    public function headings(): array
    {
        return [
            [
                'powas_id',             // A
                'user_id',              // B
                'member_id',            // C
                'member_name',          // D
                'prev_reading',         // E
                'reading',              // F
                'reading_count',        // G
                'reading_date',         // H
            ]
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $preset = PowasMembers::join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_applications.powas_id', $this->powasID)
            ->where('powas_members.member_status', 'ACTIVE')
            ->orderBy('powas_applications.lastname', 'asc')
            ->orderBy('powas_applications.firstname', 'asc')
            ->orderBy('powas_applications.middlename', 'asc')
            ->get();

        $collection = [];

        $this->toLock = [];

        foreach ($preset as $key => $value) {
            $checkReading = Readings::where('member_id', $value->member_id)
                ->orderBy('reading_date', 'DESC')
                ->first();

            if ($checkReading == null) {
                $readingCount = '';
                $prevReading = '';
            } else {
                $readingCount = $checkReading->reading_count + 1;
                $prevReading = $checkReading->reading;
                $this->toLock[] = 'G' . $key + 2;
            }

            $collection[] = [
                $this->powasID,
                $this->userID,
                $value->member_id,
                $value->lastname . ', ' . $value->firstname . ' ' . $value->middlename,
                $prevReading,
                '',
                $readingCount,
            ];
        }

        return $collection;
    }

    public function properties(): array
    {
        return [
            'title' => $this->powasID,
            'author' => $this->userID,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'F' => NumberFormat::FORMAT_NUMBER_00,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A')->getFont()->setBold(true);
        $sheet->getStyle('B')->getFont()->setBold(true);
        $sheet->getStyle('C')->getFont()->setBold(true);
        $sheet->getStyle('D')->getFont()->setBold(true);
        $sheet->getStyle('1')->getFont()->setBold(true);

        $sheet->getStyle('F2:H' . $this->memberCount + 1)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $sheet->getStyle('F2:H' . $this->memberCount + 1)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color(Color::COLOR_LIGHTYELLOW));

        foreach ($this->toLock as $value) {
            $sheet->getStyle($value)->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
            $sheet->getStyle($value)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color(Color::COLOR_WHITE));
        }

        $sheet->getStyle('A1:H' . $this->memberCount + 1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

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
    }
}
