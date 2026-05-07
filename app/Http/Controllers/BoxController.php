<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\BoxImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BoxController extends Controller
{
    public function home(): View
    {
        return view('welcome', [
            'totalBoxes' => Box::query()->count(),
            'rootBoxes' => Box::query()->whereNull('parent_id')->count(),
        ]);
    }

    public function index(): View
    {
        $boxes = Box::query()
            ->whereNull('parent_id')
            ->with('descendants.images', 'images')
            ->orderBy('code')
            ->get();

        return view('boxes.index', compact('boxes'));
    }

    public function create(Request $request): View
    {
        $parent = Box::find($request->integer('parent_id'));
        $boxes = Box::query()->orderBy('code')->get();
        $suggestedCode = $this->nextCode();

        return view('boxes.create', compact('boxes', 'parent', 'suggestedCode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:boxes,id'],
            'code' => ['required', 'string', 'max:80', 'unique:boxes,code'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['packed', 'unpacked'])],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $parent = Box::find($validated['parent_id'] ?? null);
        $code = Str::upper($validated['code']);

        $box = Box::create([
            'parent_id' => $parent?->id,
            'code' => $code,
            'qr_uuid' => (string) Str::uuid(),
            'description' => $validated['description'] ?? null,
            'level' => $parent ? $parent->level + 1 : 1,
            'status' => $validated['status'],
        ]);

        $this->storeImages($request, $box);

        return redirect()
            ->route('boxes.show', $box)
            ->with('success', __('boxes.saved'));
    }

    public function show(Box $box): View
    {
        $box->load('parent', 'children.images', 'images');
        $ancestors = $box->ancestorTrail();

        return view('boxes.show', compact('ancestors', 'box'));
    }

    public function edit(Box $box): View
    {
        $blockedParentIds = [$box->id, ...$box->descendantIds()];
        $boxes = Box::query()
            ->whereNotIn('id', $blockedParentIds)
            ->orderBy('code')
            ->get();

        return view('boxes.edit', compact('box', 'boxes'));
    }

    public function update(Request $request, Box $box): RedirectResponse
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:boxes,id'],
            'code' => ['required', 'string', 'max:80', Rule::unique('boxes', 'code')->ignore($box)],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['packed', 'unpacked'])],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $parent = Box::find($validated['parent_id'] ?? null);
        $code = Str::upper($validated['code']);

        if ($parent && in_array($parent->id, [$box->id, ...$box->descendantIds()], true)) {
            return back()
                ->withErrors(['parent_id' => __('boxes.parent_error')])
                ->withInput();
        }

        $box->update([
            'parent_id' => $parent?->id,
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'level' => $parent ? $parent->level + 1 : 1,
            'status' => $validated['status'],
        ]);

        $this->syncDescendantLevels($box);
        $this->storeImages($request, $box);

        return redirect()
            ->route('boxes.show', $box)
            ->with('success', __('boxes.updated'));
    }

    public function destroy(Box $box): RedirectResponse
    {
        $this->deleteImagesRecursively($box);

        $box->delete();

        return redirect()
            ->route('boxes.index')
            ->with('success', __('boxes.deleted'));
    }

    public function storeImagesForBox(Request $request, Box $box): RedirectResponse
    {
        $request->validate([
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $this->storeImages($request, $box);

        return back()->with('success', __('boxes.photos_uploaded'));
    }

    public function destroyImage(BoxImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', __('boxes.photo_deleted'));
    }

    public function updateNotes(Request $request, Box $box): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string'],
        ]);

        $box->update([
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', __('boxes.notes_updated'));
    }

    public function scan(): View
    {
        return view('boxes.scan');
    }

    public function showByQr(string $qrUuid): View
    {
        $box = Box::query()
            ->where('qr_uuid', $qrUuid)
            ->with('parent', 'children.images', 'images')
            ->firstOrFail();
        $ancestors = $box->ancestorTrail();

        return view('boxes.scanned', compact('ancestors', 'box'));
    }

    private function storeImages(Request $request, Box $box): void
    {
        $images = $this->uploadedFiles($request, 'images');

        foreach ($images as $image) {
            if (! $image) {
                continue;
            }

            $box->images()->create([
                'image_path' => $image->store('boxes/'.$box->code, 'public'),
            ]);
        }
    }

    private function uploadedFiles(Request $request, string $key): array
    {
        $files = $request->file($key, []);

        return is_array($files) ? $files : [$files];
    }

    private function nextCode(): string
    {
        $nextNumber = 1;

        do {
            $code = 'BOX'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (Box::query()->where('code', $code)->exists());

        return $code;
    }

    private function syncDescendantLevels(Box $box): void
    {
        $box->load('children');

        foreach ($box->children as $child) {
            $child->update(['level' => $box->level + 1]);
            $this->syncDescendantLevels($child);
        }
    }

    private function deleteImagesRecursively(Box $box): void
    {
        $box->load('images', 'children');

        foreach ($box->children as $child) {
            $this->deleteImagesRecursively($child);
        }

        foreach ($box->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
    }
}
