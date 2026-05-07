@php
    $id = $id ?? 'photo-uploader-'.Illuminate\Support\Str::uuid();
@endphp

<div
    class="border rounded-3 p-3"
    data-photo-uploader
    data-remove-label="{{ __('boxes.remove_photo') }}"
    data-camera-error="{{ __('boxes.camera_unavailable') }}"
>
    <input class="d-none" id="{{ $id }}-input" data-photo-input name="images[]" type="file" accept="image/*" multiple>

    <div class="d-flex flex-wrap gap-2 mb-2">
        <button class="btn btn-outline-primary" type="button" data-photo-select>
            {{ __('boxes.add_photos') }}
        </button>
        <button class="btn btn-outline-secondary" type="button" data-camera-open>
            {{ __('boxes.open_camera') }}
        </button>
    </div>

    <div class="form-text mb-3">{{ __('boxes.photos_help') }}</div>

    <div class="d-none mb-3" data-camera-panel>
        <video class="w-100 rounded border bg-dark mb-2" data-camera-video autoplay playsinline muted style="max-height: 360px; object-fit: cover;"></video>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary" type="button" data-camera-capture>
                {{ __('boxes.capture_photo') }}
            </button>
            <button class="btn btn-outline-secondary" type="button" data-camera-close>
                {{ __('boxes.close_camera') }}
            </button>
        </div>
    </div>

    <div class="small text-muted mb-2" data-photo-count>{{ __('boxes.selected_photos', ['count' => 0]) }}</div>
    <div class="row g-2" data-photo-preview></div>
</div>

@once
    @push('scripts')
        <script>
            document.querySelectorAll('[data-photo-uploader]').forEach((uploader) => {
                const input = uploader.querySelector('[data-photo-input]');
                const selectButton = uploader.querySelector('[data-photo-select]');
                const preview = uploader.querySelector('[data-photo-preview]');
                const count = uploader.querySelector('[data-photo-count]');
                const cameraOpen = uploader.querySelector('[data-camera-open]');
                const cameraPanel = uploader.querySelector('[data-camera-panel]');
                const cameraVideo = uploader.querySelector('[data-camera-video]');
                const cameraCapture = uploader.querySelector('[data-camera-capture]');
                const cameraClose = uploader.querySelector('[data-camera-close]');
                const removeLabel = uploader.dataset.removeLabel;
                const cameraError = uploader.dataset.cameraError;
                let files = [];
                let stream = null;

                const syncInput = () => {
                    const transfer = new DataTransfer();
                    files.forEach((file) => transfer.items.add(file));
                    input.files = transfer.files;
                };

                const render = () => {
                    preview.innerHTML = '';
                    count.textContent = @json(__('boxes.selected_photos', ['count' => '__COUNT__'])).replace('__COUNT__', files.length);

                    files.forEach((file, index) => {
                        const url = URL.createObjectURL(file);
                        const column = document.createElement('div');
                        column.className = 'col-6 col-md-3';
                        column.innerHTML = `
                            <div class="border rounded p-2 h-100">
                                <img class="img-fluid rounded object-cover w-100 mb-2" style="aspect-ratio: 1 / 1;" src="${url}" alt="${file.name}">
                                <button class="btn btn-sm btn-outline-danger w-100" type="button">${removeLabel}</button>
                            </div>
                        `;

                        column.querySelector('button').addEventListener('click', () => {
                            URL.revokeObjectURL(url);
                            files.splice(index, 1);
                            syncInput();
                            render();
                        });

                        preview.appendChild(column);
                    });
                };

                const addFiles = (newFiles) => {
                    files = files.concat(Array.from(newFiles).filter((file) => file.type.startsWith('image/')));
                    syncInput();
                    render();
                };

                const stopCamera = () => {
                    if (stream) {
                        stream.getTracks().forEach((track) => track.stop());
                        stream = null;
                    }

                    cameraPanel.classList.add('d-none');
                };

                selectButton.addEventListener('click', () => input.click());
                input.addEventListener('change', () => {
                    addFiles(input.files);
                    input.value = '';
                    syncInput();
                });

                cameraOpen.addEventListener('click', async () => {
                    if (!navigator.mediaDevices?.getUserMedia) {
                        alert(cameraError);
                        return;
                    }

                    try {
                        stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: { ideal: 'environment' } },
                            audio: false,
                        });
                        cameraVideo.srcObject = stream;
                        cameraPanel.classList.remove('d-none');
                    } catch (error) {
                        alert(cameraError);
                    }
                });

                cameraCapture.addEventListener('click', () => {
                    const canvas = document.createElement('canvas');
                    canvas.width = cameraVideo.videoWidth || 1280;
                    canvas.height = cameraVideo.videoHeight || 720;
                    canvas.getContext('2d').drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob((blob) => {
                        if (!blob) {
                            return;
                        }

                        addFiles([
                            new File([blob], `camera-${Date.now()}.jpg`, { type: 'image/jpeg' })
                        ]);
                    }, 'image/jpeg', 0.92);
                });

                cameraClose.addEventListener('click', stopCamera);
                uploader.closest('form')?.addEventListener('submit', stopCamera);
                render();
            });
        </script>
    @endpush
@endonce
