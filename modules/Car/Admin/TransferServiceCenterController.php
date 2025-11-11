<?php

namespace Modules\Car\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AdminController;
use Modules\Car\Models\TransferLocation;
use Modules\Car\Models\TransferServiceCenter;
use Modules\User\Models\User;

class TransferServiceCenterController extends AdminController
{
    protected TransferServiceCenter $centers;

    public function __construct(TransferServiceCenter $centers)
    {
        $this->centers = $centers;
        $this->setActiveMenu(route('car.admin.transfer-service-centers.index'));
    }

    public function index(Request $request): View
    {
        $this->checkPermission('car_manage_attributes');

        $query = $this->centers->newQuery()->with(['vendor', 'location']);
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
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->integer('location_id'));
        }

        $rows = $query->orderBy('name')->paginate(20);

        return view('Car::admin.transferServiceCenters.index', [
            'rows' => $rows,
            'locations' => TransferLocation::query()->orderBy('name')->get(),
            'vendors' => User::query()->orderBy('name')->limit(50)->get(),
            'page_title' => __('transfers.admin.service_centers.title'),
        ]);
    }

    public function create(): View
    {
        $this->checkPermission('car_manage_attributes');

        return view('Car::admin.transferServiceCenters.detail', [
            'row' => new TransferServiceCenter(),
            'locations' => TransferLocation::query()->orderBy('name')->get(),
            'vendors' => User::query()->orderBy('name')->limit(50)->get(),
            'page_title' => __('transfers.admin.service_centers.create_title'),
        ]);
    }

    public function edit(int $id): View
    {
        $this->checkPermission('car_manage_attributes');
        $row = $this->centers->findOrFail($id);

        return view('Car::admin.transferServiceCenters.detail', [
            'row' => $row,
            'locations' => TransferLocation::query()->orderBy('name')->get(),
            'vendors' => User::query()->orderBy('name')->limit(50)->get(),
            'page_title' => __('transfers.admin.service_centers.edit_title', ['name' => $row->name]),
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
            'vendor_id' => ['nullable', 'integer', 'exists:users,id'],
            'location_id' => ['nullable', 'integer', 'exists:car_transfer_locations,id'],
        ]);

        $center = $id ? $this->centers->findOrFail($id) : $this->centers->newInstance();
        $center->fill($data);
        $center->save();

        return redirect()->route('car.admin.transfer-service-centers.edit', $center)
            ->with('success', __('transfers.admin.service_centers.saved_message'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission('car_manage_attributes');
        $center = $this->centers->findOrFail($id);
        $center->delete();

        return redirect()->route('car.admin.transfer-service-centers.index')
            ->with('success', __('transfers.admin.service_centers.deleted_message'));
    }
}
