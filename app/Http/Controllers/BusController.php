<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusController extends Controller
{
    // Get all buses with their assigned drivers
    public function index()
    {
        $buses = Bus::with('driver')->get();

        foreach ($buses as $bus) {
            $bus->driver_name = $bus->driver?->name;
            $bus->driver_email = $bus->driver?->email;
        }

        return response()->json($buses);
    }

    // Get one bus by bus number
    public function show($bus_number)
    {
        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found',
            ], 404);
        }

        $etaMinutes = null;

        if (
            $bus->latitude !== null &&
            $bus->longitude !== null &&
            $bus->destination_latitude !== null &&
            $bus->destination_longitude !== null
        ) {
            $earthRadius = 6371;

            $lat1 = deg2rad((float) $bus->latitude);
            $lon1 = deg2rad((float) $bus->longitude);
            $lat2 = deg2rad((float) $bus->destination_latitude);
            $lon2 = deg2rad((float) $bus->destination_longitude);

            $dLat = $lat2 - $lat1;
            $dLon = $lon2 - $lon1;

            $a = sin($dLat / 2) ** 2
                + cos($lat1) * cos($lat2)
                * sin($dLon / 2) ** 2;

            $a = max(0, min(1, $a));

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            $distanceKm = $earthRadius * $c;
            $etaMinutes = max(1, (int) round(($distanceKm / 30) * 60));
        }

        return response()->json([
            'id' => $bus->id,
            'bus_number' => $bus->bus_number,
            'route' => $bus->route,
            'status' => $bus->status,
            'latitude' => $bus->latitude,
            'longitude' => $bus->longitude,
            'destination_latitude' => $bus->destination_latitude,
            'destination_longitude' => $bus->destination_longitude,
            'location_updated_at' => $bus->location_updated_at,
            'eta_minutes' => $etaMinutes,
        ]);
    }

    // Add a new bus
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bus_number' => 'required|string|unique:buses,bus_number',
            'route' => 'required|string',
            'status' => 'nullable|in:On Time,Delayed',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'destination_latitude' => 'nullable|numeric|between:-90,90',
            'destination_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $bus = Bus::create($validatedData);

        return response()->json($bus, 201);
    }

    // Update bus details
    public function update(Request $request, $bus_number)
    {
        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found',
            ], 404);
        }

        $validatedData = $request->validate([
            'route' => 'required|string',
            'status' => 'required|in:On Time,Delayed',
            'destination_latitude' => 'nullable|numeric|between:-90,90',
            'destination_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $bus->update($validatedData);

        return response()->json([
            'message' => 'Bus updated successfully',
            'bus' => $bus,
        ]);
    }

    // Update bus location from driver GPS
    public function updateLocation(Request $request, $bus_number)
    {
        $user = $request->user();

        if (
            $user &&
            $user->role === 'driver' &&
            $user->bus_number !== $bus_number
        ) {
            return response()->json([
                'message' => 'You are not assigned to this bus.',
            ], 403);
        }

        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found',
            ], 404);
        }

        $validatedData = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $bus->update([
            ...$validatedData,
            'location_updated_at' => now(),
        ]);

        return response()->json($bus);
    }

    // Assign a driver to a bus
    public function assignDriver(Request $request, $bus_number)
    {
        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found',
            ], 404);
        }

        $validatedData = $request->validate([
            'driver_id' => 'required|integer|exists:users,id',
        ]);

        $driver = User::where('id', $validatedData['driver_id'])
            ->where('role', 'driver')
            ->first();

        if (!$driver) {
            return response()->json([
                'message' => 'Selected user is not a driver.',
            ], 422);
        }

        DB::transaction(function () use ($bus, $driver) {
            // Release any other bus assigned to this driver.
            Bus::where('driver_id', $driver->id)
                ->where('id', '!=', $bus->id)
                ->update(['driver_id' => null]);

            // Release any other driver assigned to this bus.
            User::where('role', 'driver')
                ->where('bus_number', $bus->bus_number)
                ->where('id', '!=', $driver->id)
                ->update(['bus_number' => null]);

            // Synchronize both assignment fields.
            $bus->driver_id = $driver->id;
            $bus->save();

            $driver->bus_number = $bus->bus_number;
            $driver->save();
        });

        return response()->json([
            'message' => 'Driver assigned successfully',
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'bus_number' => $bus->bus_number,
            ],
        ]);
    }

    // Delete a bus
    public function destroy($bus_number)
    {
        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found',
            ], 404);
        }

        // Clear the driver's assignment before deleting the bus.
        User::where('role', 'driver')
            ->where('bus_number', $bus->bus_number)
            ->update(['bus_number' => null]);

        $bus->delete();

        return response()->json([
            'message' => 'Bus deleted successfully',
        ]);
    }
}