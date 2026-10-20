<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json(['success' => true, 'addresses' => $addresses]);
    }

    public function store(Request $request)
    {
        // Strip non-numeric characters from phone
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $request->phone);
        $request->merge(['phone' => $cleanPhone]);

        $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'nullable|string|max:100',
            'phone' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $clean = preg_replace('/[^0-9]/', '', (string) $value);
                    if (!str_starts_with($clean, '0')) {
                        $fail('The phone number must start with 0.');
                    } elseif (str_starts_with($clean, '088') || str_starts_with($clean, '096')) {
                        if (strlen($clean) !== 10) {
                            $fail('Phone numbers starting with 088 or 096 must be exactly 10 digits.');
                        }
                    } else {
                        if (strlen($clean) !== 9) {
                            $fail('Phone numbers must be exactly 9 digits (or 10 digits for 088 and 096).');
                        }
                    }
                },
            ],
            'address_line' => 'required|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Save phone to user profile if empty
        if (empty($user->phone)) {
            $user->update(['phone' => $cleanPhone]);
        }

        // If marked default or first address, unset previous defaults
        $isDefault = (bool) $request->input('is_default', false) || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create([
            'label' => $request->label,
            'recipient_name' => $user->name, // Strictly follows user account name and cannot be changed
            'phone' => $cleanPhone,
            'address_line' => $request->address_line,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'notes' => $request->notes,
            'is_default' => $isDefault,
        ]);

        $distanceKm = $address->getDistanceFromStore();
        $etaMinutes = $address->getEtaMinutes();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Address saved successfully.',
                'address' => $address,
                'distance_km' => $distanceKm,
                'eta_minutes' => $etaMinutes,
            ]);
        }

        return redirect()->back()->with('success', 'Address saved successfully.');
    }

    public function setDefault($id)
    {
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        $user->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Default address updated.']);
        }

        return redirect()->back()->with('success', 'Default address updated.');
    }

    public function destroy($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Address deleted.']);
        }

        return redirect()->back()->with('success', 'Address deleted.');
    }

    public function calculateDistance(Request $request)
    {
        $lat = (float) $request->input('latitude', 11.5564);
        $lng = (float) $request->input('longitude', 104.9282);

        $tempAddress = new UserAddress(['latitude' => $lat, 'longitude' => $lng]);
        $distance = $tempAddress->getDistanceFromStore();
        $eta = $tempAddress->getEtaMinutes();

        return response()->json([
            'distance_km' => $distance,
            'eta_minutes' => $eta,
            'formatted' => "{$distance} km (~{$eta} mins)",
        ]);
    }
}
