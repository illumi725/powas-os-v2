<?php

namespace App\Livewire;

use App\Models\Billings;
use App\Models\Powas;
use App\Models\PowasMembers;
use App\Rules\AccountNumberPattern;
use Livewire\Component;

class AccountSearch extends Component
{
    public $accountnumber;
    public $billingInfo;
    public $availableBillings = [];
    public $memberInfo;
    public $powasInfo;

    public $showingSearchResultModal = false;

    protected $rules;

    protected $validationAttributes = [
        'accountnumber' => 'account number',
    ];

    public function __construct()
    {
        $this->rules = [
            'accountnumber' => ['required', new AccountNumberPattern],
        ];
    }

    public function render()
    {
        return view('livewire.account-search');
    }

    public function showSearchResultModal()
    {
        $this->accountnumber = strtoupper($this->accountnumber);
        $this->validate();
        $this->resetErrorBag();

        $this->reset([
            'billingInfo',
            'availableBillings',
            'memberInfo',
            'powasInfo',
        ]);

        $this->memberInfo = PowasMembers::find($this->accountnumber);

        if ($this->memberInfo == null) {
            $this->dispatch('notfound', [
                'message' => 'Account number \'' . $this->accountnumber . '\' cannot be found!',
                'messageType' => 'error',
                'position' => 'top-right',
            ]);

            return;
        }

        $this->powasInfo = Powas::find($this->memberInfo->applicationinfo->powas_id);

        $this->billingInfo = Billings::join('powas_members', 'billings.member_id', '=', 'powas_members.member_id')
            ->join('powas_applications', 'powas_members.application_id', '=', 'powas_applications.application_id')
            ->where('powas_members.member_id', $this->accountnumber)
            ->where('billings.bill_status', 'UNPAID')
            ->orderByDesc('billings.billing_month')->get();

        $this->showingSearchResultModal = true;
    }
}
