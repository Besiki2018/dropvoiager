<?php

namespace Modules\Car\Controllers\Vendor;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Car\Models\TransferLocation;
use Modules\Car\Models\TransferServiceCenter;
use Modules\FrontendController;

class TransferServiceCenterController extends FrontendController
{
    protected TransferServiceCenter $centers;

    public function __construct(TransferServiceCenter $centers)
    {
        parent::__construct();
        $this->centers = $centers;
    }

    public function index(Request $request)
    {
        $this->checkPermission('car_view');

        $query = $this->centers->newQuery()
            ->where('vendor_id', Auth::id())
            ->orderBy('name');

        if ($request->filled('s')) {
            $search = $request->string('s');
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->integer('location_id'));
        }

        /** @var LengthAwarePaginator $rows */
        $rows = $query->with('location')->paginate(10);

        $locations = TransferLocation::query()
            ->where('vendor_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('Car::frontend.vendor.transferServiceCenters.index', [
            'rows' => $rows,
            'locations' => $locations,
            'page_title' => __('transfers.admin.service_centers.title'),
        ]);
    }

    public function create()
    {
        $this->checkPermission('car_create');

        $locations = TransferLocation::query()
            ->where('vendor_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('Car::frontend.vendor.transferServiceCenters.detail', [
            'row' => new TransferServiceCenter(),
            'locations' => $locations,
            'page_title' => __('transfers.admin.service_centers.create_title'),
        ]);
    }

    public function edit(int $id)
    {
        $this->checkPermission('car_update');
        $row = $this->centers->where('vendor_id', Auth::id())->findOrFail($id);

        $locations = TransferLocation::query()
            ->where('vendor_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('Car::frontend.vendor.transferServiceCenters.detail', [
            'row' => $row,
            'locations' => $locations,
            'page_title' => __('transfers.admin.service_centers.edit_title', ['name' => $row->name]),
        ]);
    }

    public function store(Request $request, int $id = 0): RedirectResponse
    {
        $this->checkPermission($id ? 'car_update' : 'car_create');

        $locationIds = TransferLocation::query()
            ->where('vendor_id', Auth::id())
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'location_id' => ['nullable', 'integer', function ($attribute, $value, $fail) use ($locationIds) {
                if ($value && !in_array($value, $locationIds, true)) {
                    $fail(__('transfers.admin.service_centers.location_any'));
                }
            }],
        ]);

        $center = $id
            ? $this->centers->where('vendor_id', Auth::id())->findOrFail($id)
            : $this->centers->newInstance(['vendor_id' => Auth::id()]);

        $center->fill($data);
        $center->vendor_id = Auth::id();
        if (!in_array($center->location_id, $locationIds, true)) {
            $center->location_id = null;
        }
        $center->save();

        return redirect()->route('car.vendor.transfer-service-centers.edit', $center)
            ->with('success', __('transfers.admin.service_centers.saved_message'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkPermission('car_delete');
        $center = $this->centers->where('vendor_id', Auth::id())->findOrFail($id);
        $center->delete();

        return redirect()->route('car.vendor.transfer-service-centers.index')
            ->with('success', __('transfers.admin.service_centers.deleted_message'));
    }
}
