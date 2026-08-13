<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->latest('is_default')->latest()->get();
        return view('frontend.user.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:120',
            'state' => 'nullable|string|max:120',
            'zip' => 'nullable|string|max:30',
            'country' => 'required|string|max:120',
            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($data) {
            $user = auth()->user();
            $makeDefault = (bool)($data['is_default'] ?? false) || !$user->addresses()->exists();
            if ($makeDefault) {
                $user->addresses()->update(['is_default' => false]);
            }
            $data['is_default'] = $makeDefault;
            $user->addresses()->create($data);
        });

        return back()->with('success', 'Address saved successfully.');
    }

    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $data = $request->validate([
            'label' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:120',
            'state' => 'nullable|string|max:120',
            'zip' => 'nullable|string|max:30',
            'country' => 'required|string|max:120',
            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($address, $data) {
            if (!empty($data['is_default'])) {
                Address::where('user_id', auth()->id())->whereKeyNot($address->id)->update(['is_default' => false]);
            }
            $address->update($data);
        });

        return back()->with('success', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $wasDefault = $address->is_default;
        $address->delete();
        if ($wasDefault) {
            auth()->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }
        return back()->with('success', 'Address removed.');
    }

    public function makeDefault(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        DB::transaction(function () use ($address) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });
        return back()->with('success', 'Default address updated.');
    }
}
