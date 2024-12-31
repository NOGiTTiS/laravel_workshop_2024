<?php

namespace App\Livewire;

use App\Models\BillingModel;
use App\Models\RoomModel;
use Livewire\Component;

class Billing extends Component
{
    public $showModal = false;
    public $showModalDelete = false;
    public $showModalGetMoney = false;
    public $rooms = [];
    public $billings = [];
    public $id;
    public $roomId;
    public $remark;
    public $createdAt;
    public $status;
    public $amountRent;
    public $amountWater;
    public $amountElectric;
    public $amountInternet;
    public $amountFitness;
    public $amountWash;
    public $amountBin;
    public $amountEtc;
    public $customerName;
    public $customerPhone;
    public $listStatus = [
        ['status' => 'wait', 'name' => 'รอชำระเงิน'],
        ['status' => 'paid', 'name' => 'ชำระเงินแล้ว'],
        ['status' => 'next', 'name' => 'ขอค้างจ่าย'],
    ];
    public $sumAmount = 0;
    public $roomForDelete;
    public $waterUnit = 0;
    public $electricUnit = 0;
    public $waterCostPerUnit = 0;
    public $electricCostPerUnit = 0;
    public $roomNameForEdit = '';

    // get money
    public $roomNameForGetMoney = '';
    public $customerNameForGetMoney = '';
    public $payedDateForGetMoney = '';
    public $moneyAdded = 0;
    public $remarkForGetMoney = '';
    public $sumAmountForGetMoney = 0;
    public $amountForGetMoney = 0;
    public $billing;

    public function mount()
    {
        $this->fetchData();
        $this->createdAt = date('Y-m-d');
        $this->status = 'wait';
    }

    public function fetchData()
    {
        $this->rooms = RoomModel::where('is_empty', 'no')
            ->where('status', 'use')
            ->orderBy('id', 'desc')
            ->get();

        $this->billings = BillingModel::orderBy('id', 'desc')->get();
        $roomNoBilling = [];

        foreach ($this->rooms as $room) {
            foreach ($this->billngs as $billing) {
                if ($billing->room_id == $room->id) {
                    $roomNoBilling[] = $room;
                }
            }
        }

        $this->rooms = $roomNoBilling;
    }

    public function render()
    {
        return view('livewire.billing');
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function selectedRoom()
    {
        $room = RoomModel::find($this->roomId);
        $customer = CustomerModel::where('room_id', $this->roomId)->first();
        // $organization = OrganizationModel::first();

        // if ($organization->amount_water > 0) {
        //     $this->amountWater = $organization->amount_water;
        // } else {
        //     $this->waterCostPerUnit = $organization->amount_water_per_unit;
        // }

        // if ($organization->amount_electric_per_unit > 0) {
        //     $this->electricCostPerUnit = $organization->amount_electric_per_unit;
        // }

        // $this->amountInternet = $organization->amount_internet;
        // $this->amountEtc = $organization->amount_etc;

        $this->customerName = $customer->name;
        $this->customerPhone = $customer->phone;
        $this->amountRent = $room->amount_rent;

        $this->computeSumAmount();
    }

    public function computeSumAmount()
    {
        $this->sumAmount = $this->amountRent + $this->amountWater + $this->amountElectric
         + $this->amountInternet + $this->amountFitness + $this->amountWash
         + $this->amountBin + $this->amountEtc;
    }

    public function save()
    {
        $billing = new BillingModel();

        if ($this->id != null) {
            $billing = BillingModel::find($this->id);
        }

        $billing->room_id = $this->roomId;
        $billing->created_at = $this->createdAt;
        $billing->status = $this->status;
        $billing->remark = $this->remark ?? '';
        $billing->amount_rent = $this->amountRent ?? 0;
        $billing->amount_water = $this->amountWater ?? 0;
        $billing->amount_electric = $this->amountElectric ?? 0;
        $billing->amount_internet = $this->amountInternet ?? 0;
        $billing->amount_fitness = $this->amountFitness ?? 0;
        $billing->amount_wash = $this->amountWash ?? 0;
        $billing->amount_bin = $this->amountBin ?? 0;
        $billing->amount_etc = $this->amountEtc ?? 0;
        $billing->save();

        $this->fetchData();
        $this->closeModal();

        $this->id = null;
        $this->waterUnit = 0;
        $this->electricUnit = 0;
        $this->electricCostPerUnit = 0;
        $this->waterCostPerUnit = 0;
    }
}
