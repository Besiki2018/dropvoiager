<?php

namespace Modules\Car\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Car\Models\Car;
use Modules\Car\Models\CarPickupLocation;
use Modules\FrontendController;

class ManagePickupLocationController extends FrontendController
{
    protected CarPickupLocation $locations;
    protected Car $cars;

    public function __construct(CarPickupLocation $locations, Car $cars)
    {
        parent::__construct();
        $this->locations = $locations;
        $this->cars = $cars;
    }

    public function index(Request $request)
    {
        $this->checkPermission('car_view');

        $userId = Auth::id();
        $query = $this->locations->newQuery()
            ->with(['car:id,title'])
            ->forOwner($userId)
            ->orderByDesc('updated_at');

        if ($search = $request->query('s')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('address', 'LIKE', '%' . $search . '%');
            });
        }

        $rows = $query->paginate(10)->withQueryString();

        return view('Car::frontend.managePickupLocations.index', [
            'rows' => $rows,
            'page_title' => __('transfers.admin.pickup_locations.vendor_title'),
            'breadcrumbs' => [
                ['name' => __('transfers.admin.pickup_locations.vendor_title'), 'url' => route('car.vendor.pickup-locations.index')],
            ],
        ]);
    }

    public function create()
    {
        $this->checkPermission('car_update');

        return view('Car::frontend.managePickupLocations.detail', [
            'row' => $this->locations->newInstance(['is_active' => true]),
            'cars' => $this->cars->newQuery()->where('author_id', Auth::id())->orderBy('title')->get(),
            'page_title' => __('transfers.admin.pickup_locations.vendor_create'),
        ]);
    }

    public function edit(int $id)
    {
        $this->checkPermission('car_update');

        $location = $this->locations->newQuery()->forOwner(Auth::id())->findOrFail($id);

        return view('Car::frontend.managePickupLocations.detail', [
            'row' => $location,
            'cars' => $this->cars->newQuery()->where('author_id', Auth::id())->orderBy('title')->get(),
            'page_title' => __('transfers.admin.pickup_locations.vendor_edit', ['name' => $location->display_name ?: $location->name ?: '#' . $location->id]),
        ]);
    }

    public function store(Request $request, int $id = 0)
    {
        $this->checkPermission('car_update');

        $userId = Auth::id();

        if ($id > 0) {
            $location = $this->locations->newQuery()->forOwner($userId)->findOrFail($id);
        } else {
            $location = $this->locations->newInstance();
            $location->vendor_id = $userId;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'car_id' => ['nullable', 'integer', 'exists:bravo_cars,id'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'map_zoom' => ['nullable', 'integer', 'min:1', 'max:20'],
            'service_center_name' => ['nullable', 'string', 'max:255'],
            'service_center_address' => ['nullable', 'string', 'max:255'],
            'service_center_lat' => ['nullable', 'numeric'],
            'service_center_lng' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['car_id'])) {
            $car = $this->cars->newQuery()->where('author_id', $userId)->findOrFail($data['car_id']);
            $location->car_id = $car->id;
            $location->vendor_id = $car->author_id;
            if (empty($data['map_zoom']) && !empty($car->map_zoom)) {
                $location->map_zoom = (int) $car->map_zoom;
            }
        } else {
            $location->car_id = null;
        }

        $location->fill($data);
        $location->is_active = (bool) ($data['is_active'] ?? false);
        $location->vendor_id = $userId;
        $location->save();

        return redirect()->route('car.vendor.pickup-locations.edit', ['id' => $location->id])
            ->with('success', __('transfers.admin.pickup_locations.saved_message'));
    }

    public function destroy(int $id)
    {
        $this->checkPermission('car_update');

        $location = $this->locations->newQuery()->forOwner(Auth::id())->findOrFail($id);
        $location->delete();

        return redirect()->route('car.vendor.pickup-locations.index')
            ->with('success', __('transfers.admin.pickup_locations.deleted_message'));
    }
}
