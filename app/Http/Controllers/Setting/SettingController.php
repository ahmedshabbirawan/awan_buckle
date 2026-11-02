<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class SettingController extends Controller
{
    public $name;
    public $adminURL;
    public $viewData;
    public $view;

    public function __construct()
    {
        $this->name             = $this->viewData['name']             = 'System Settings';
        $this->adminURL         = $this->viewData['adminURL']         = url('Settings/system');
        $this->view             = 'settings.';
        $this->viewData['view'] = $this->view;
    }

    /**
     * Display listing of settings (DataTables AJAX or full view).
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $results = Setting::query()->orderBy('id', 'ASC');

            return DataTables::of($results)
                ->addColumn('is_essential_badge', function ($row) {
                    if ($row->isEssential()) {
                        return '<span class="label label-sm label-warning"><i class="fa fa-lock"></i> Essential</span>';
                    }
                    return '<span class="label label-sm label-info">Custom</span>';
                })
                ->addColumn('formatted_value', function ($row) {
                    if (is_null($row->value) || $row->value === '') {
                        return '<em class="text-muted">(not set)</em>';
                    }
                    $display = htmlspecialchars($row->value);
                    if (strlen($display) > 60) {
                        return '<span title="' . $display . '">' . substr($display, 0, 60) . '...</span>';
                    }
                    return $display;
                })
                ->addColumn('action', function ($row) {
                    $action = '<div class="btn-group">';
                    $action .= '<a href="' . route('Settings.setting.edit', $row->id) . '" class="btn btn-xs btn-info" data-toggle="tooltip" title="Edit Setting"><i class="ace-icon fa fa-pencil bigger-120"></i></a>';

                    if ($row->isEssential()) {
                        $action .= '<button type="button" class="btn btn-xs btn-default disabled" data-toggle="tooltip" title="Essential setting cannot be deleted" style="cursor:not-allowed;"><i class="ace-icon fa fa-trash-o bigger-120"></i></button>';
                    } else {
                        $action .= '<a href="javascript:void(0);" onclick="deleteConfirmation(' . $row->id . ', \'' . addslashes($row->key) . '\');" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Delete Setting"><i class="ace-icon fa fa-trash-o bigger-120"></i></a>';
                    }

                    $action .= '</div>';
                    return $action;
                })
                ->rawColumns(['is_essential_badge', 'formatted_value', 'action'])
                ->make(true);
        }

        $allSettings = Setting::getAllSettings();
        $this->viewData['settings'] = $allSettings;
        $this->viewData['essentialKeys'] = Setting::ESSENTIAL_KEYS;

        return view($this->view . 'lists', $this->viewData);
    }

    /**
     * Show form for creating a new setting.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->view . 'create', $this->viewData);
    }

    /**
     * Store a newly created setting.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->merge([
            'key' => strtolower(trim((string) $request->key))
        ]);

        $validated = $request->validate([
            'key'   => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:settings,key'],
            'value' => ['nullable', 'string'],
        ], [
            'key.regex'  => 'The setting key may only contain lowercase letters, numbers, and underscores (e.g., custom_tax_rate).',
            'key.unique' => 'A setting with this key already exists.',
        ]);

        Setting::create($validated);
        Setting::clearCache();

        return redirect()->route('Settings.setting.index')
            ->with('success', 'Setting "' . $validated['key'] . '" has been successfully created.');
    }

    /**
     * Show form for editing an existing setting.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        $this->viewData['setting'] = $setting;

        return view($this->view . 'edit', $this->viewData);
    }

    /**
     * Update an existing setting.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        if ($setting->isEssential()) {
            // Essential setting keys cannot be renamed to protect system integrity
            $validated = $request->validate([
                'value' => ['nullable', 'string'],
            ]);

            $setting->value = $validated['value'] ?? null;
            $setting->save();
        } else {
            $request->merge([
                'key' => strtolower(trim((string) $request->key))
            ]);

            $validated = $request->validate([
                'key'   => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', Rule::unique('settings', 'key')->ignore($setting->id)],
                'value' => ['nullable', 'string'],
            ], [
                'key.regex' => 'The setting key may only contain lowercase letters, numbers, and underscores.',
            ]);

            $setting->key   = $validated['key'];
            $setting->value = $validated['value'] ?? null;
            $setting->save();
        }

        Setting::clearCache();

        return redirect()->route('Settings.setting.index')
            ->with('success', 'Setting "' . $setting->key . '" has been updated successfully.');
    }

    /**
     * Delete a setting from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        // Prevent accidental deletion of essential system settings
        if ($setting->isEssential()) {
            if ($request->ajax()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Cannot delete essential system setting "' . $setting->key . '".',
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Cannot delete essential system setting "' . $setting->key . '".');
        }

        $key = $setting->key;
        $setting->delete();
        Setting::clearCache();

        if ($request->ajax()) {
            return response()->json([
                'status'  => true,
                'message' => 'Setting "' . $key . '" has been deleted successfully.',
            ]);
        }

        return redirect()->route('Settings.setting.index')
            ->with('success', 'Setting "' . $key . '" has been deleted successfully.');
    }

    /**
     * Batch / quick update essential settings from the business profile form.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function quickUpdate(Request $request)
    {
        $request->validate([
            'settings' => ['required', 'array'],
        ]);

        foreach ($request->settings as $key => $val) {
            $key = strtolower(trim((string) $key));
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val]
            );
        }

        Setting::clearCache();

        return redirect()->route('Settings.setting.index')
            ->with('success', 'General business settings have been successfully updated.');
    }

    /**
     * Manually refresh / clear the system settings cache.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCache()
    {
        Setting::clearCache();

        return redirect()->back()
            ->with('success', 'System settings cache has been cleared and synchronized with the database.');
    }
}
