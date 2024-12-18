<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserProfile extends Component
{
    public $tab = null;
    public $tabname = 'personal_details';

    protected $queryString = ['tab' => ['keep' => true]];

    public $name, $username, $email, $phone, $address;

    public $current_password, $new_password, $new_password_confirmation;

    public function selectTab($tab)
    {
        $this->tab = $tab;
    }

    public function mount()
    {
        $this->tab = request()->tab ? request()->tab : $this->tabname;

        $consignor = User::findOrFail(auth('user')->id());
        $this->name = $consignor->name;
        $this->username = $consignor->username;
        $this->email = $consignor->email;
        $this->phone = $consignor->phone;
        $this->address = $consignor->address;
    }

    public function updateUserPersonalDetails()
    {
        $this->validate([
            'name' => 'required|min:5',
            'username' => 'nullable|min:5|unique:users,username,' . auth('user')->id(),
        ]);
        $consignor = User::findOrFail(auth('user')->id());
        $consignor->name = $this->name;
        $consignor->username = $this->username;
        // $consignor->phone = $this->phone;
        // $consignor->address = $this->address;
        $update = $consignor->save();

        if ($update) {
            $this->dispatch('updateAdminHeaderInfo');
            $this->dispatch('toast', type: 'success', message: 'Personal details updated successfully.');
        } else {
            $this->dispatch('toast', type: 'error', message: 'Something went wrong.');
        }
    }

    public function updatePassword()
    {
        $consignor = User::findOrFail(auth('user')->id());

        $this->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, User::find(auth('user')->id())->password)) {
                        return $fail(_('The current password is incorrect'));
                    }
                }
            ],

            'new_password' => 'required|min:5|max:45|confirmed'
        ]);

        $update = $consignor->update([
            'password' => Hash::make($this->new_password)
        ]);

        if ($update) {
            $data['consignor'] = $consignor;
            $data['new_password'] = $this->new_password;

            $mail_body = view('email-templates.user-reset-email-template', $data);

            $mailConfig = array(
                'mail_from_email' => env('EMAIL_FROM_ADDRESS'),
                'mail_from_name' => env('EMAIL_FROM_NAME'),
                'mail_recipient_email' => $consignor->email,
                'mail_recipient_name' => $consignor->name,
                'mail_subject' => 'Password changed',
                'mail_body' => $mail_body
            );

            sendEmail($mailConfig);
            $this->current_password = null;
            $this->new_password = null;
            $this->new_password_confirmation = null;
            $this->dispatch('toast', type: 'success', message: 'Password changed successfully.');
        } else {
            $this->dispatch('toast', type: 'error', message: 'Something went wrong.');
        }
    }

    public function render()
    {
        return view('livewire.user.user-profile', [
            'user' => User::findOrFail(auth('user')->id())
        ]);
    }
}
