<?php

namespace Modules\Car\Controllers\Vendor;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Car\Models\TransferLocation;
use Modules\FrontendController;

class TransferLocationController extends FrontendController
{
    protected TransferLocation $locations;

    public function __construct(TransferLocation $locations)
    {
        parent::__construct();
        $this->locations = $locations;
    }

    public function index(Request $request)
    {
        $this->checkPermission('car_view');

        $query = $this->locations->newQuery()
            ->where('vendor_id', Auth::id())
            ->orderBy('name');

        if ($request->filled('s')) {
            $search = $request->string('s');
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        /** @var LengthAwarePaginator $rows */
        $rows = $query->paginate(10);

        return view('Car::frontend.vendor.transferLocations.index', [
            'rows' => $rows,
            'page_title' => __('transfers.admin.locations.title'),
        ]);
    }

    public function create()
    {
        $this->checkPermission('car_create');

        return view('Car::frontend.vendor.transferLocations.detail', [
            'row' => new TransferLocation(),
            'page_title' => __('transfers.admin.locations.create_title'),
        ]);
    }

    public function edit(int $id)
    {
        $this->checkPermission('car_update');
        $row = $this->locations->where('vendor_id', Auth::id())->findOrFail($id);

        return view('Car::frontend.vendor.transferLocations.detail', [
            'row' => $row,
            'page_title' => __('transfers.admin.locations.edit_title', ['name' => $row->name]),
        ]);
    }

    public function store(Request $request, int $id = 0): RedirectResponse
    {
        $this->checkPermission($id ? 'car_update' : 'car_create');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'map_zoom' => ['nullable', 'integer', 'between:1,20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $location = $id
            ? $this->locations->where('vendor_id', Auth::id())->findOrFail($id)
            : $this->locations->newInstance(['vendor_id' => Auth::id()]);

        $location->fill($data);
        if (!$request->has('is_active')) {
            $location->is_active = $location->is_active ?? true;
        }
        $location->vendor_id = Auth::id();
        $location->save();

        return redirect()->route('car.vendor.transfer-locations.edit', $location)
            ->with('success', __('transfers.admin.locations.saved_message'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission('car_delete');
        $location = $this->locations->where('vendor_id', Auth::id())->findOrFail($id);
        $location->delete();

        return redirect()->route('car.vendor.transfer-locations.index')
            ->with('success', __('transfers.admin.locations.deleted_message'));
    }
}
