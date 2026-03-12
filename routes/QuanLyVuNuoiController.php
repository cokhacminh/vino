<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\DatabaseHelper;
use App\Models\Farm;
use App\Models\VuNuoiTom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class QuanLyVuNuoiController extends Controller
{
    /**
     * Hiển thị danh sách farm để chọn
     */
    public function index()
    {
        $farms = Farm::where('is_active', true)->orderBy('name')->get();
        return view('admin.quanlyvunuoi.index', compact('farms'));
    }

    /**
     * Hiển thị danh sách vụ nuôi của farm
     */
    public function list($farmId, Request $request)
    {
        $farm = Farm::findOrFail($farmId);
        $connectionName = DatabaseHelper::getFarmConnectionName($farmId);
        
        try {
            // Query vụ nuôi
            $query = VuNuoiTom::on($connectionName);
            
            $vuNuoiList = $query->orderBy('MaVuNuoi', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching vunuoitom for farm', [
                'farm_id' => $farmId,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.quanlyvunuoi.index')
                ->with('error', 'Lỗi khi kết nối database: ' . $e->getMessage());
        }

        return view('admin.quanlyvunuoi.list', compact('farm', 'vuNuoiList'));
    }

    /**
     * Lấy thông tin vụ nuôi (dùng cho AJAX edit)
     */
    public function getVuNuoi($farmId, $id)
    {
        $connectionName = DatabaseHelper::getFarmConnectionName($farmId);
        try {
            $vuNuoi = VuNuoiTom::on($connectionName)->findOrFail($id);
            return response()->json(['success' => true, 'data' => $vuNuoi]);
        } catch (\Exception $e) {
            Log::error('Error fetching vunuoitom for modal', [
                'farm_id' => $farmId,
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'message' => 'Không tìm thấy vụ nuôi'], 404);
        }
    }

    /**
     * Lưu vụ nuôi mới
     */
    public function store(Request $request, $farmId)
    {
        $farm = Farm::findOrFail($farmId);
        $connectionName = DatabaseHelper::getFarmConnectionName($farmId);

        $validated = $request->validate([
            'NgayThaGiong' => 'required|date',
            'SoLuongGiong' => 'nullable|integer|min:0',
            'TongTienGiong' => 'nullable|integer|min:0',
            'TinhTrang' => 'nullable|string|max:255',
        ]);

        try {
            // Tự động tạo MaVuNuoi nếu không có
            if (empty($validated['MaVuNuoi'])) {
                $maxId = VuNuoiTom::on($connectionName)->max('MaVuNuoi');
                $maxId = $maxId ? (int)$maxId : 0;
                $validated['MaVuNuoi'] = $maxId + 1;
            }

            // Convert date strings to Carbon if needed
            if (isset($validated['NgayThaGiong']) && is_string($validated['NgayThaGiong'])) {
                $validated['NgayThaGiong'] = Carbon::parse($validated['NgayThaGiong'])->format('Y-m-d');
            }

            // Convert TongTienGiong from formatted string to number
            if (isset($validated['TongTienGiong']) && is_string($validated['TongTienGiong'])) {
                $validated['TongTienGiong'] = (int)str_replace(['.', ','], '', $validated['TongTienGiong']);
            }

            VuNuoiTom::on($connectionName)->create($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tạo vụ nuôi thành công'
                ]);
            }

            return redirect()->route('admin.quanlyvunuoi.list', $farmId)
                ->with('success', 'Tạo vụ nuôi thành công');
        } catch (\Exception $e) {
            Log::error('Error creating vunuoitom', [
                'farm_id' => $farmId,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi tạo vụ nuôi: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()
                ->with('error', 'Lỗi khi tạo vụ nuôi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật vụ nuôi
     */
    public function update(Request $request, $farmId, $id)
    {
        $farm = Farm::findOrFail($farmId);
        $connectionName = DatabaseHelper::getFarmConnectionName($farmId);

        $validated = $request->validate([
            'NgayThaGiong' => 'sometimes|required|date',
            'SoLuongGiong' => 'nullable|integer|min:0',
            'TongTienGiong' => 'nullable|integer|min:0',
            'TinhTrang' => 'nullable|string|max:255',
            'NgayKetThuc' => 'nullable|date',
        ]);

        try {
            // Convert date strings to Carbon if needed
            if (isset($validated['NgayThaGiong']) && is_string($validated['NgayThaGiong'])) {
                $validated['NgayThaGiong'] = Carbon::parse($validated['NgayThaGiong'])->format('Y-m-d');
            }
            if (isset($validated['NgayKetThuc']) && is_string($validated['NgayKetThuc'])) {
                $validated['NgayKetThuc'] = Carbon::parse($validated['NgayKetThuc'])->format('Y-m-d');
            }

            // Convert TongTienGiong from formatted string to number
            if (isset($validated['TongTienGiong']) && is_string($validated['TongTienGiong'])) {
                $validated['TongTienGiong'] = (int)str_replace(['.', ','], '', $validated['TongTienGiong']);
            }

            $vuNuoi = VuNuoiTom::on($connectionName)->findOrFail($id);
            $vuNuoi->update($validated);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật vụ nuôi thành công'
                ]);
            }

            return redirect()->route('admin.quanlyvunuoi.list', $farmId)
                ->with('success', 'Cập nhật vụ nuôi thành công');
        } catch (\Exception $e) {
            Log::error('Error updating vunuoitom', [
                'farm_id' => $farmId,
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi cập nhật vụ nuôi: ' . $e->getMessage()
                ], 422);
            }

            return back()->withInput()
                ->with('error', 'Lỗi khi cập nhật vụ nuôi: ' . $e->getMessage());
        }
    }

    /**
     * Xóa vụ nuôi
     */
    public function destroy($farmId, $id)
    {
        $farm = Farm::findOrFail($farmId);
        $connectionName = DatabaseHelper::getFarmConnectionName($farmId);

        try {
            $vuNuoi = VuNuoiTom::on($connectionName)->findOrFail($id);
            $vuNuoi->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xóa vụ nuôi thành công'
                ]);
            }

            return redirect()->route('admin.quanlyvunuoi.list', $farmId)
                ->with('success', 'Xóa vụ nuôi thành công');
        } catch (\Exception $e) {
            Log::error('Error deleting vunuoitom', [
                'farm_id' => $farmId,
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi xóa vụ nuôi: ' . $e->getMessage()
                ], 422);
            }

            return back()
                ->with('error', 'Lỗi khi xóa vụ nuôi: ' . $e->getMessage());
        }
    }
}

