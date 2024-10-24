<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Inventory as InventoryModel;
use App\Models\Consignment;
use App\Models\User;

class ConsignPost extends Component
{
    use WithFileUploads;

    public $name, $brand, $sku, $color, $size, $description, $picture, $visibility, $sex, $purchase_price, $selling_price, $commission_percentage, $qty, $consignor_id, $consignor_name, $start_date, $expiry_date;

    protected $rules = [
        'name' => 'required',
        'brand' => 'required',
        'sku' => 'required',
        'color' => 'required',
        'size' => 'required|min:0',
        'qty' => 'required|integer|min:0',
        'description' => 'nullable',
        'picture' => 'nullable|image',
        'visibility' => 'required',
        'purchase_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'commission_percentage' => 'required|numeric|min:0',
        'qty' => 'required|integer|min:1',
        'consignor_name' => 'required',
        'start_date' => 'required|date',
        'expiry_date' => 'required|date|after:start_date'
    ];

    public function updatedConsignorId($value)
    {
        $this->showConsignorName($value);
    }

    public function showConsignorName($consignorId)
    {
        $consignor = User::find($consignorId);
        if ($consignor) {
            $this->consignor_name = $consignor->name;
        } else {
            $this->consignor_name = null;
        }
    }

    public function store()
    {
        $validatedData = $this->validate();
        $consignor =  User::find($this->consignor_id);

        $consignment = new Consignment();
        $consignment->commission_percentage = $validatedData['commission_percentage'];
        $consignment->start_date = $validatedData['start_date'];
        $consignment->expiry_date = $validatedData['expiry_date'];

        $consignment->user()->associate($consignor);
        $consignment->save();

        if ($this->picture) {
            $filename = 'IMG_' . uniqid() . '.' . $this->picture->getClientOriginalExtension();
            $validatedData['picture'] = $filename;
            $this->picture->storeAs('images/consignments/', $filename, 'public');
        }

        $inventory = new InventoryModel();
        // $inventory->consignment_id = $consignment->id;
        $inventory->name = $validatedData['name'];
        $inventory->brand = $validatedData['brand'];
        $inventory->sku = $validatedData['sku'];
        $inventory->color = $validatedData['color'];
        $inventory->size = $validatedData['size'];
        $inventory->description = $validatedData['description'];
        $inventory->picture = $validatedData['picture'];
        $inventory->visibility = $validatedData['visibility'];
        $inventory->purchase_price = $validatedData['purchase_price'];
        $inventory->selling_price = $validatedData['selling_price'];
        $inventory->qty = $validatedData['qty'];

        $inventory->consignment()->associate($consignment);
        $inventory->save();

        $this->reset();
        $this->dispatch('toast', type: 'success', message: 'Consignment added successfully.');
    }

    public function render()
    {
        return view('livewire.admin.consign-post', [
            'rows' => User::orderBy('email', 'ASC')->get()
        ]);
    }
}
