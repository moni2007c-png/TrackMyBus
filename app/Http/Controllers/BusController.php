<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\User;
use Illuminate\Http\Request;

class BusController extends Controller
{
    // Get all buses
    public function index()
    {
        $buses = Bus::all();

        foreach ($buses as $bus) {
            $driver = User::where('role', 'driver')
                ->where('bus_number', $bus->bus_number)
                ->first();

            $bus->driver_name = $driver?->name;
            $bus->driver_email = $driver?->email;
        }

        return response()->json($buses);
    }

    // Get one bus by bus number
    public function show($bus_number)
    {
        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found'
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

            $lat1 = deg2rad($bus->latitude);
            $lon1 = deg2rad($bus->longitude);
            $lat2 = deg2rad($bus->destination_latitude);
            $lon2 = deg2rad($bus->destination_longitude);

            $dLat = $lat2 - $lat1;
            $dLon = $lon2 - $lon1;

            $a = sin($dLat / 2) ** 2
                + cos($lat1) * cos($lat2)
                * sin($dLon / 2) ** 2;

            $c = 2 * atan2(
                sqrt($a),
                sqrt(1 - $a)
            );

            $distanceKm = $earthRadius * $c;

            // Assume average bus speed of 30 km/h
            $etaMinutes = round(($distanceKm / 30) * 60);

            if ($etaMinutes < 1) {
                $etaMinutes = 1;
            }
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
            'bus_number' => 'required|unique:buses',
            'route' => 'required',
            'status' => 'nullable',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'destination_latitude' => 'nullable|numeric',
            'destination_longitude' => 'nullable|numeric',
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
                'message' => 'Bus not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'route' => 'required',
            'status' => 'required|in:On Time,Delayed',
            'destination_latitude' => 'nullable|numeric',
            'destination_longitude' => 'nullable|numeric',
        ]);

        $bus->update($validatedData);

        return response()->json([
            'message' => 'Bus updated successfully',
            'bus' => $bus
        ]);
    }

    // Update bus location from driver GPS
    public function updateLocation(Request $request, $bus_number)
    {
        // Make sure the driver can update only their assigned bus
        if (
            auth()->user()->role === 'driver' &&
            auth()->user()->bus_number !== $bus_number
        ) {
            return response()->json([
                'message' => 'You are not assigned to this bus.'
            ], 403);
        }

        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $bus->update(array_merge($validatedData, [
            'location_updated_at' => now(),
        ]));

        return response()->json($bus);
    }

    // Assign a driver to a bus
    public function assignDriver(Request $request, $bus_number)
    {
        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $driver = User::where('id', $validatedData['driver_id'])
            ->where('role', 'driver')
            ->first();

        if (!$driver) {
            return response()->json([
                'message' => 'Selected user is not a driver.'
            ], 422);
        }

        // Remove this bus assignment from any other driver
        User::where('role', 'driver')
            ->where('bus_number', $bus_number)
            ->update([
                'bus_number' => null
            ]);

        // Assign the selected driver
        $driver->update([
            'bus_number' => $bus_number
        ]);

        return response()->json([
            'message' => 'Driver assigned successfully',
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'bus_number' => $driver->bus_number,
            ]
        ]);
    }

    // Delete a bus
    public function destroy($bus_number)
    {
        $bus = Bus::where('bus_number', $bus_number)->first();

        if (!$bus) {
            return response()->json([
                'message' => 'Bus not found'
            ], 404);
        }

        $bus->delete();

        return response()->json([
            'message' => 'Bus deleted successfully'
        ]);
    }
}