<?php

namespace App\Imports;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\ChartOfAccounts;
use App\Models\IssuedReceipts;
use App\Models\PowasApplications;
use App\Models\PowasMembers;
use App\Models\PowasSettings;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class MembersImport implements ToModel, WithHeadingRow, WithColumnFormatting
{
    use Importable;

    public $comView = 'livewire.powas.members-cards-list';

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $POWASApplicationModel = new PowasApplications;
        $POWASMembersModel = new PowasMembers;
        $TransactionsModel = new Transactions;
        $IssuedReceiptsModel = new IssuedReceipts;

        $membershipDate = Carbon::parse(Date::excelToDateTimeObject($row['membership_date']));
        $cashOnHandAccountNumber = ChartOfAccounts::where('account_type', 'ASSET')->where('account_number', '101')->first();

        $toCheck = [
            'lastname' => strtoupper($row['lastname']),
            'firstname' => strtoupper($row['firstname']),
            'barangay' => strtoupper($row['barangay']),
            'municipality' => strtoupper($row['municipality']),
            'province' => strtoupper($row['province']),
            'region' => strtoupper($row['region']),
        ];

        $isExisting = PowasApplications::isExisting($toCheck);
        $powasSettings = PowasSettings::where('powas_id', $row['powas_id'])->first();
        $importer = User::find($row['user_id']);

        if (!$isExisting) {
            $application_id = rand(10000000, 99999999);
            $member_id = $row['powas_id'] . '-' . rand(1000, 9999);

            $POWASApplicationModel->create([
                'application_id' => $application_id,
                'powas_id' => strtoupper($row['powas_id']),
                'lastname' => strtoupper($row['lastname']),
                'firstname' => strtoupper($row['firstname']),
                'middlename' => strtoupper($row['middlename']),
                'birthday' => Date::excelToDateTimeObject($row['birthday']),
                'birthplace' => strtoupper($row['birthplace']),
                'gender' => strtoupper($row['gender']),
                'contact_number' => strtoupper($row['contact_number']),
                'civil_status' => strtoupper($row['civil_status']),
                'address1' => strtoupper($row['address1']),
                'barangay' => strtoupper($row['barangay']),
                'municipality' => strtoupper($row['municipality']),
                'province' => strtoupper($row['province']),
                'region' => strtoupper($row['region']),
                'present_address' => strtoupper($row['present_address']),
                'family_members' => $row['family_members'],
                'application_status' => strtoupper($row['application_status']),
                'by_user_id' =>  $row['user_id'],
                'application_date' => $membershipDate,
                'add_mode' => 'import',
                'id_path' => null,
            ]);

            $logMessage = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created application details for <b><i>' . $row['lastname'] . ', ' . $row['firstname'] . '</i></b> via Excel import.';
            ActionLogger::dispatch('create', $logMessage, Auth::user()->user_id, 'applications', $row['powas_id']);

            $POWASMembersModel->create([
                'member_id' => $member_id,
                'application_id' => $application_id,
                'meter_number' => $row['meter_number'],
                'membership_date' => $membershipDate,
                'firstfifty' => $row['firstfifty'],
                'land_owner' => $row['land_owner'],
                'member_status' => $row['member_status'],
            ]);

            $logMessage = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created member account for <b><i>' . $row['lastname'] . ', ' . $row['firstname'] . '</i></b> via Excel import.';
            ActionLogger::dispatch('create', $logMessage, Auth::user()->user_id, 'members', $row['powas_id']);

            if ($row['firstfifty'] == 'Y') {
                $equityCapitalAccountNumber = ChartOfAccounts::where('account_type', 'EQUITY')->where('account_name', 'LIKE', '%' . 'CAPITAL' . '%')->first();

                $journalEntryNumber = CustomNumberFactory::journalEntryNumber($row['powas_id'], $membershipDate);

                // Transaction for Equity Account
                $trxnNewID = CustomNumberFactory::getRandomID();
                $description = 'Equity Capital received from ' . $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'];

                $TransactionsModel->create([
                    'trxn_id' => $trxnNewID,
                    'account_number' => $equityCapitalAccountNumber->account_number,
                    'description' => $description,
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $powasSettings->first_50_fee,
                    'transaction_side' => $equityCapitalAccountNumber->normal_balance,
                    'received_from' => strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']),
                    'member_id' => $member_id,
                    'powas_id' => $row['powas_id'],
                    'recorded_by_id' => $row['user_id'],
                    'transaction_date' => $membershipDate,
                ]);

                $log_message = '<b><u>' . $importer->userinfo->lastname . ', ' . $importer->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($equityCapitalAccountNumber->account_name) . '</i></b> with description <b>"' . $description . '"</b> amounting to <b>&#8369;' . number_format($powasSettings->first_50_fee, 2) . '</b>.';

                ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $row['powas_id']);

                // Transaction for Cash Account
                $trxnNewID = CustomNumberFactory::getRandomID();

                $description = 'Cash received from ' . $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'] . ' for Equity Capital';

                Transactions::create([
                    'trxn_id' => $trxnNewID,
                    'account_number' => $cashOnHandAccountNumber->account_number,
                    'description' => $description,
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $powasSettings->first_50_fee,
                    'transaction_side' => $cashOnHandAccountNumber->normal_balance,
                    'received_from' => $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'],
                    'member_id' => $member_id,
                    'powas_id' => $row['powas_id'],
                    'recorded_by_id' => Auth::user()->user_id,
                    'transaction_date' => $membershipDate,
                ]);

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccountNumber->account_name) . '</i></b> with description <b>"' . $description . '"</b> amounting to <b>&#8369;' . number_format($powasSettings->first_50_fee, 2) . '</b>.';

                ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $row['powas_id']);

                $printNewID = CustomNumberFactory::getRandomID();

                $receiptNumber = CustomNumberFactory::receipt($row['powas_id'], $membershipDate);

                $IssuedReceiptsModel->create([
                    'print_id' => $printNewID,
                    'receipt_number' => $receiptNumber,
                    'trxn_id' => $trxnNewID,
                    'powas_id' => $row['powas_id'],
                    'transaction_date' => $membershipDate,
                ]);
            } else {
                $applicationFeeAccountNumber = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'APPLICATION FEE' . '%')->first();

                $membershipFeeAccountNumber = ChartOfAccounts::where('account_type', 'REVENUE')->where('account_name', 'LIKE', '%' . 'MEMBERSHIP FEE' . '%')->first();

                $journalEntryNumber = CustomNumberFactory::journalEntryNumber($row['powas_id'], $membershipDate);

                // Transaction for Application Fee
                $trxnNewID = CustomNumberFactory::getRandomID();

                $description = 'Application Fee received from ' . $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'];

                $TransactionsModel->create([
                    'trxn_id' => $trxnNewID,
                    'account_number' => $applicationFeeAccountNumber->account_number,
                    'description' => $description,
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $powasSettings->application_fee,
                    'transaction_side' => $applicationFeeAccountNumber->normal_balance,
                    'received_from' => strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']),
                    'member_id' => $member_id,
                    'powas_id' => $row['powas_id'],
                    'recorded_by_id' => $row['user_id'],
                    'transaction_date' => $membershipDate,
                ]);

                $log_message = '<b><u>' . $importer->userinfo->lastname . ', ' . $importer->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($applicationFeeAccountNumber->account_name) . '</i></b> with description <b>"' . $description . '"</b> amounting to <b>&#8369;' . number_format($powasSettings->application_fee, 2) . '</b>.';

                ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $row['powas_id']);

                // Transaction for Cash Account
                $trxnNewID = CustomNumberFactory::getRandomID();

                $description = 'Cash received from ' . $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'] . ' for Application Fee';

                Transactions::create([
                    'trxn_id' => $trxnNewID,
                    'account_number' => $cashOnHandAccountNumber->account_number,
                    'description' => $description,
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $powasSettings->application_fee,
                    'transaction_side' => $cashOnHandAccountNumber->normal_balance,
                    'received_from' => $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'],
                    'member_id' => $member_id,
                    'powas_id' => $row['powas_id'],
                    'recorded_by_id' => Auth::user()->user_id,
                    'transaction_date' => $membershipDate,
                ]);

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccountNumber->account_name) . '</i></b> with description <b>"' . $description . '"</b> amounting to <b>&#8369;' . number_format($powasSettings->application_fee, 2) . '</b>.';

                ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $row['powas_id']);

                $printNewID = CustomNumberFactory::getRandomID();

                $receiptNumber = CustomNumberFactory::receipt($row['powas_id'], $membershipDate);

                $IssuedReceiptsModel->create([
                    'print_id' => $printNewID,
                    'receipt_number' => $receiptNumber,
                    'trxn_id' => $trxnNewID,
                    'powas_id' => $row['powas_id'],
                    'transaction_date' => $membershipDate,
                ]);

                $journalEntryNumber = CustomNumberFactory::journalEntryNumber($row['powas_id'], $membershipDate);

                // Transaction for Membership Fee
                $trxnNewID = CustomNumberFactory::getRandomID();
                $description = 'Membership Fee received from ' . $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'];

                $TransactionsModel->create([
                    'trxn_id' => $trxnNewID,
                    'account_number' => $membershipFeeAccountNumber->account_number,
                    'description' => $description,
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $powasSettings->membership_fee,
                    'transaction_side' => $membershipFeeAccountNumber->normal_balance,
                    'received_from' => strtoupper($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']),
                    'member_id' => $member_id,
                    'powas_id' => $row['powas_id'],
                    'recorded_by_id' => $row['user_id'],
                    'transaction_date' => $membershipDate,
                ]);

                $log_message = '<b><u>' . $importer->userinfo->lastname . ', ' . $importer->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($membershipFeeAccountNumber->account_name) . '</i></b> with description <b>"' . $description . '"</b> amounting to <b>&#8369;' . number_format($powasSettings->membership_fee, 2) . '</b>.';

                ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $row['powas_id']);

                // Transaction for Cash Account
                $trxnNewID = CustomNumberFactory::getRandomID();

                $description = 'Cash received from ' . $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'] . ' for Membership Fee';

                Transactions::create([
                    'trxn_id' => $trxnNewID,
                    'account_number' => $cashOnHandAccountNumber->account_number,
                    'description' => $description,
                    'journal_entry_number' => $journalEntryNumber,
                    'amount' => $powasSettings->membership_fee,
                    'transaction_side' => $cashOnHandAccountNumber->normal_balance,
                    'received_from' => $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'],
                    'member_id' => $member_id,
                    'powas_id' => $row['powas_id'],
                    'recorded_by_id' => Auth::user()->user_id,
                    'transaction_date' => $membershipDate,
                ]);

                $log_message = '<b><u>' . Auth::user()->userinfo->lastname . ', ' . Auth::user()->userinfo->firstname . '</u></b> created transaction for <b><i>' . strtoupper($cashOnHandAccountNumber->account_name) . '</i></b> with description <b>"' . $description . '"</b> amounting to <b>&#8369;' . number_format($powasSettings->membership_fee, 2) . '</b>.';

                ActionLogger::dispatch('create', $log_message, Auth::user()->user_id, 'transactions', $row['powas_id']);

                $printNewID = CustomNumberFactory::getRandomID();

                $IssuedReceiptsModel->create([
                    'print_id' => $printNewID,
                    'receipt_number' => $receiptNumber,
                    'trxn_id' => $trxnNewID,
                    'powas_id' => $row['powas_id'],
                    'transaction_date' => $membershipDate,
                ]);
            }

            // return new PowasMembers([
            //     'member_id' => $member_id,
            //     'application_id' => $application_id,
            //     'meter_number' => $row['meter_number'],
            //     'membership_date' => $membershipDate,
            //     'firstfifty' => $row['firstfifty'],
            //     'member_status' => $row['member_status'],
            // ]);
        }

        // return null;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'R' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
