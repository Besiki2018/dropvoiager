<?php

namespace Modules\Car\Admin;

use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\Car\Models\TransferRoute;

class TransferRouteController extends AdminController
{
    protected TransferRoute $routes;

    public function __construct(TransferRoute $routes)
    {
        $this->routes = $routes;
        $this->setActiveMenu(route('car.admin.transfer-routes.index'));
    }

    public function index(Request $request)
    {
        $this->checkPermission('car_manage_attributes');
        $query = $this->routes->newQuery();
        if ($request->filled('s')) {
            $search = $request->input('s');
            $query->where(function ($q) use ($search) {
                $q->where('pickup_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('dropoff_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE', '%' . $search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $rows = $query->orderBy('sort_order')->orderBy('pickup_name')->paginate(20);
        return view('Car::admin.transferRoutes.index', [
            'rows' => $rows,
            'page_title' => __('transfers.admin.routes.title'),
            'breadcrumbs' => [
                [
                    'name' => __('transfers.admin.routes.title'),
                    'url' => route('car.admin.transfer-routes.index'),
                ],
            ],
        ]);
    }

    public function create()
    {
        $this->checkPermission('car_manage_attributes');
        return view('Car::admin.transferRoutes.detail', [
            'row' => new TransferRoute(),
            'page_title' => __('transfers.admin.routes.create_title'),
        ]);
    }

    public function edit(int $id)
    {
        $this->checkPermission('car_manage_attributes');
        $row = $this->routes->findOrFail($id);
        return view('Car::admin.transferRoutes.detail', [
            'row' => $row,
            'page_title' => __('transfers.admin.routes.edit_title', ['name' => $row->display_name]),
        ]);
    }

    public function store(Request $request, int $id = 0)
    {
        $this->checkPermission('car_manage_attributes');
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'pickup_name' => ['required', 'string', 'max:255'],
            'pickup_address' => ['nullable', 'string', 'max:255'],
            'pickup_lat' => ['nullable', 'numeric'],
            'pickup_lng' => ['nullable', 'numeric'],
            'dropoff_name' => ['required', 'string', 'max:255'],
            'dropoff_address' => ['nullable', 'string', 'max:255'],
            'dropoff_lat' => ['nullable', 'numeric'],
            'dropoff_lng' => ['nullable', 'numeric'],
            'status' => ['required', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer'],
        ]);
        $route = $id ? $this->routes->findOrFail($id) : $this->routes->newInstance();
        $route->fill($data);
        $route->save();
        return redirect()->route('car.admin.transfer-routes.edit', ['id' => $route->id])
            ->with('success', __('transfers.admin.routes.saved_message'));
    }

    public function bulkEdit(Request $request)
    {
        $this->checkPermission('car_manage_attributes');
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) || !is_array($ids) || empty($action)) {
            return redirect()->back()->with('error', __('transfers.admin.routes.bulk_error'));
        }
        $routes = $this->routes->withTrashed()->whereIn('id', $ids)->get();
        foreach ($routes as $route) {
            switch ($action) {
                case 'delete':
                    $route->delete();
                    break;
                case 'restore':
                    if ($route->trashed()) {
                        $route->restore();
                    }
                    break;
                default:
                    if (in_array($action, ['publish', 'draft'])) {
                        $route->status = $action;
                        $route->save();
                    }
                    break;
            }
        }
        return redirect()->back()->with('success', __('transfers.admin.routes.bulk_success'));
    }
}
