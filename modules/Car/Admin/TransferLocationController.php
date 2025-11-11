<?php

namespace Modules\Car\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AdminController;
use Modules\Car\Models\TransferLocation;
use Modules\User\Models\User;

class TransferLocationController extends AdminController
{
    protected TransferLocation $locations;

    public function __construct(TransferLocation $locations)
    {
        $this->locations = $locations;
        $this->setActiveMenu(route('car.admin.transfer-locations.index'));
    }

    public function index(Request $request): View
    {
        $this->checkPermission('car_manage_attributes');

        $query = $this->locations->newQuery()->with('vendor');
        if ($request->filled('s')) {
            $search = $request->string('s');
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->integer('vendor_id'));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $rows = $query->orderBy('name')->paginate(20);

        return view('Car::admin.transferLocations.index', [
            'rows' => $rows,
            'vendors' => User::query()->orderBy('name')->limit(50)->get(),
            'page_title' => __('transfers.admin.locations.title'),
        ]);
    }

    public function create(): View
    {
        $this->checkPermission('car_manage_attributes');

        return view('Car::admin.transferLocations.detail', [
            'row' => new TransferLocation(),
            'vendors' => User::query()->orderBy('name')->limit(50)->get(),
            'page_title' => __('transfers.admin.locations.create_title'),
        ]);
    }

    public function edit(int $id): View
    {
        $this->checkPermission('car_manage_attributes');
        $row = $this->locations->findOrFail($id);

        return view('Car::admin.transferLocations.detail', [
            'row' => $row,
            'vendors' => User::query()->orderBy('name')->limit(50)->get(),
            'page_title' => __('transfers.admin.locations.edit_title', ['name' => $row->name]),
        ]);
    }

    public function store(Request $request, int $id = 0): RedirectResponse
    {
        $this->checkPermission('car_manage_attributes');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'map_zoom' => ['nullable', 'integer', 'between:1,20'],
            'vendor_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $location = $id ? $this->locations->findOrFail($id) : $this->locations->newInstance();
        $location->fill($data);
        if (!$request->has('is_active')) {
            $location->is_active = $location->is_active ?? true;
        }
        $location->save();

        return redirect()->route('car.admin.transfer-locations.edit', $location)
            ->with('success', __('transfers.admin.locations.saved_message'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission('car_manage_attributes');
        $location = $this->locations->findOrFail($id);
        $location->delete();

        return redirect()->route('car.admin.transfer-locations.index')
            ->with('success', __('transfers.admin.locations.deleted_message'));
    }
}
