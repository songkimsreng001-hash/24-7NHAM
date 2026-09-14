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
        $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:25',
            'address_line' => 'required|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // If marked default or first address, unset previous defaults
        $isDefault = (bool) $request->input('is_default', false) || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create([
            'label' => $request->label,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
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
