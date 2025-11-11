<?php

namespace Modules\Car\Admin;

use App\User;
use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\Car\Models\Car;
use Modules\Car\Models\CarPickupLocation;

class PickupLocationController extends AdminController
{
    protected CarPickupLocation $locations;

    public function __construct(CarPickupLocation $locations)
    {
        parent::__construct();
        $this->locations = $locations;
        $this->setActiveMenu(route('car.admin.pickup-locations.index'));
    }

    public function index(Request $request)
    {
        $this->checkPermission('car_manage_attributes');

        $query = $this->locations->newQuery()->with(['car:id,title', 'vendor:id,name,email']);

        if ($search = $request->query('s')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('address', 'LIKE', '%' . $search . '%')
                    ->orWhere('service_center_name', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('car_id')) {
            $query->where('car_id', $request->query('car_id'));
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->query('vendor_id'));
        }

        if ($request->filled('status')) {
            $status = $request->query('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $rows = $query->orderByDesc('updated_at')->paginate(20)->withQueryString();

        return view('Car::admin.pickupLocations.index', [
            'rows' => $rows,
            'page_title' => __('transfers.admin.pickup_locations.title'),
            'cars' => Car::query()->select(['id', 'title'])->orderBy('title')->get(),
            'vendors' => User::query()->select(['id', 'name'])->orderBy('name')->limit(200)->get(),
        ]);
    }

    public function create()
    {
        $this->checkPermission('car_manage_attributes');

        return view('Car::admin.pickupLocations.detail', [
            'row' => $this->locations->newInstance(['is_active' => true]),
            'cars' => Car::query()->select(['id', 'title'])->orderBy('title')->get(),
            'vendors' => User::query()->select(['id', 'name'])->orderBy('name')->limit(200)->get(),
            'page_title' => __('transfers.admin.pickup_locations.create_title'),
        ]);
    }

    public function edit(int $id)
    {
        $this->checkPermission('car_manage_attributes');

        $row = $this->locations->newQuery()->with(['car:id,title', 'vendor:id,name'])->findOrFail($id);

        return view('Car::admin.pickupLocations.detail', [
            'row' => $row,
            'cars' => Car::query()->select(['id', 'title'])->orderBy('title')->get(),
            'vendors' => User::query()->select(['id', 'name'])->orderBy('name')->limit(200)->get(),
            'page_title' => __('transfers.admin.pickup_locations.edit_title', ['name' => $row->display_name ?: $row->name ?: '#' . $row->id]),
        ]);
    }

    public function store(Request $request, int $id = 0)
    {
        $this->checkPermission('car_manage_attributes');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'place_id' => ['nullable', 'string', 'max:255'],
            'car_id' => ['nullable', 'integer', 'exists:bravo_cars,id'],
            'vendor_id' => ['nullable', 'integer', 'exists:users,id'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'map_zoom' => ['nullable', 'integer', 'min:1', 'max:20'],
            'service_center_name' => ['nullable', 'string', 'max:255'],
            'service_center_address' => ['nullable', 'string', 'max:255'],
            'service_center_lat' => ['nullable', 'numeric'],
            'service_center_lng' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $location = $id ? $this->locations->findOrFail($id) : $this->locations->newInstance();

        $location->fill($data);
        $location->is_active = (bool) ($data['is_active'] ?? false);

        if (empty($data['vendor_id']) && !empty($data['car_id'])) {
            $car = Car::query()->select(['id', 'author_id', 'map_zoom'])->find($data['car_id']);
            if ($car) {
                $location->vendor_id = $car->author_id;
                if (empty($location->map_zoom) && !empty($car->map_zoom)) {
                    $location->map_zoom = (int) $car->map_zoom;
                }
            }
        }

        $location->save();

        return redirect()->route('car.admin.pickup-locations.edit', ['id' => $location->id])
            ->with('success', __('transfers.admin.pickup_locations.saved_message'));
    }

    public function bulkEdit(Request $request)
    {
        $this->checkPermission('car_manage_attributes');

        $ids = $request->input('ids');
        $action = $request->input('action');

        if (empty($ids) || !is_array($ids) || empty($action)) {
            return redirect()->back()->with('error', __('transfers.admin.pickup_locations.bulk_error'));
        }

        $query = $this->locations->newQuery()->whereIn('id', $ids);
        $locations = $query->get();

        foreach ($locations as $location) {
            switch ($action) {
                case 'activate':
                    $location->is_active = true;
                    $location->save();
                    break;
                case 'deactivate':
                    $location->is_active = false;
                    $location->save();
                    break;
                case 'delete':
                    $location->delete();
                    break;
            }
        }

        return redirect()->back()->with('success', __('transfers.admin.pickup_locations.bulk_success'));
    }
}
