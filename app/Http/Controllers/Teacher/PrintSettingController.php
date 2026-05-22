<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExamPrintLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PrintSettingController extends Controller
{
    private const DEFAULT_SCHOOL_NAME = 'SMK UJIAN TERUS';
    private const DEFAULT_SCHOOL_DEPARTMENT = 'Multimedia dan TBSM';
    private const DEFAULT_SCHOOL_ADDRESS = 'Jl. Selalu Memikirkan Ujian';

    public function edit(Request $request): View
    {
        $setting = $request->user()->printSetting()->firstOrNew([], $this->defaultSettingData());

        return view('teacher.settings.print', [
            'setting' => $setting,
            'previewLogoUrl' => $this->resolvePreviewLogoUrl(
                $setting->logo_path,
                $setting->updated_at?->timestamp
            ),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_department' => ['required', 'string', 'max:255'],
            'school_address' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_logo' => ['nullable', 'boolean'],
        ], [
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'school_department.required' => 'Jurusan wajib diisi.',
            'school_address.required' => 'Alamat sekolah wajib diisi.',
            'logo.image' => 'File logo harus berupa gambar.',
            'logo.mimes' => 'Logo hanya boleh berformat JPG, JPEG, PNG, atau WEBP.',
            'logo.max' => 'Ukuran logo maksimal 4 MB.',
        ]);

        $setting = $teacher->printSetting()->firstOrCreate([], $this->defaultSettingData());

        $logoPath = $setting->logo_path;

        if ($request->boolean('remove_logo') && $logoPath) {
            Storage::disk('public')->delete($logoPath);
            $logoPath = null;
        }

        if ($request->hasFile('logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            $logoPath = $request->file('logo')->store('teacher-print-logos', 'public');
        }

        $setting->update([
            'school_name' => $data['school_name'],
            'school_department' => $data['school_department'],
            'school_address' => $data['school_address'],
            'logo_path' => $logoPath,
        ]);

        return back()->with('status', 'Pengaturan print berhasil diperbarui.');
    }

    public function reset(Request $request): RedirectResponse
    {
        $teacher = $request->user();
        $setting = $teacher->printSetting()->firstOrCreate([], $this->defaultSettingData());

        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }

        $setting->update([
            ...$this->defaultSettingData(),
            'logo_path' => null,
        ]);

        return back()->with('status', 'Pengaturan print berhasil direset ke kondisi default.');
    }

    public function showLogo(Request $request): BinaryFileResponse
    {
        $setting = $request->user()->printSetting;

        abort_unless($setting?->logo_path && Storage::disk('public')->exists($setting->logo_path), 404);

        return response()->file(
            Storage::disk('public')->path($setting->logo_path),
            [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    public function history(Request $request): View
    {
        $logs = ExamPrintLog::query()
            ->where('teacher_id', $request->user()->id)
            ->with(['exam.subject'])
            ->latest('printed_at')
            ->paginate(15);

        return view('teacher.settings.print-history', [
            'logs' => $logs,
        ]);
    }

    private function resolvePreviewLogoUrl(?string $logoPath, ?int $version = null): ?string
    {
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            return route('teacher.settings.print.logo', [
                'v' => $version ?? time(),
            ]);
        }

        return null;
    }

    private function defaultSettingData(): array
    {
        return [
            'school_name' => self::DEFAULT_SCHOOL_NAME,
            'school_department' => self::DEFAULT_SCHOOL_DEPARTMENT,
            'school_address' => self::DEFAULT_SCHOOL_ADDRESS,
        ];
    }
}
