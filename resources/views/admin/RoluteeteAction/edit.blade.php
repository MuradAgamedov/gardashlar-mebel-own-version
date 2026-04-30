@extends('admin.layout.app')

@section('title', __('Edit Roulette Action'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>{{ __('Edit Roulette Action') }}</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="material-icons-outlined">home</i>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.roulette-actions.index') }}">{{ __('Actions') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Edit Action') }}</h5>
                    </div>
                    <div class="card-body">
                        {{-- Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.roulette-actions.update', $action->id) }}" 
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Current Image --}}
                            @if($action->image)
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label><br>
                                    <img src="{{ asset('storage/' . $action->image) }}" 
                                         alt="{{ __('Current Image') }}" style="max-width:200px; border:1px solid #ddd; border-radius:5px;">
                                </div>
                            @endif

                            {{-- Upload New Image --}}
                            <div class="mb-3">
                                <label for="image" class="form-label">{{ __('Image') }}</label>
                                <input type="file" name="image" id="image"
                                       class="form-control @error('image') is-invalid @enderror"
                                       accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Preview --}}
                                <div class="mt-2">
                                    <img id="imagePreview" src="#" alt="{{ __('Preview') }}"
                                         style="max-width: 200px; display: none; border:1px solid #ddd; padding:5px; border-radius:5px;">
                                </div>
                            </div>

                            {{-- Type --}}
                            <div class="mb-3">
                                <label for="type" class="form-label">{{ __('Type') }}</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">{{ __('-- Select Type --') }}</option>
                                    <option value="percent" {{ old('type', optional($action->translates->first())->type) == 'percent' ? 'selected' : '' }}>{{ __('Percent') }}</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Percent field --}}
                            <div class="mb-3 {{ old('type', optional($action->translates->first())->type) == 'percent' ? '' : 'd-none' }}" id="percentField">
                                <label for="percent_value" class="form-label">{{ __('Percent Value') }}</label>
                                <input type="number" 
                                       class="form-control @error('percent_value') is-invalid @enderror" 
                                       id="percent_value" 
                                       name="percent" 
                                       value="{{ old('percent', $action->percent) }}" 
                                       min="1" max="100">
                                @error('percent_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Translations --}}
                            <ul class="nav nav-tabs" role="tablist">
                                @foreach($languages as $lang)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link @if($loop->first) active @endif" 
                                                id="tab-{{ $lang->id }}" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#lang-{{ $lang->id }}" 
                                                type="button" role="tab">
                                            {{ $lang->lang_name }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content mt-3">
                                @foreach($languages as $lang)
                                    @php
                                        $translation = $action->translates->where('language_id', $lang->id)->first();
                                    @endphp
                                    <div class="tab-pane fade @if($loop->first) show active @endif" 
                                         id="lang-{{ $lang->id }}" role="tabpanel">

                                        {{-- Title --}}
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Title') }} ({{ $lang->lang_code }})</label>
                                            <input type="text" 
                                                   name="translations[{{ $lang->id }}][title]" 
                                                   class="form-control @error("translations.$lang->id.title") is-invalid @enderror" 
                                                   value="{{ old("translations.$lang->id.title", optional($translation)->title) }}">
                                            @error("translations.$lang->id.title")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Type (hidden per translation) --}}
                                        <input type="hidden" name="translations[{{ $lang->id }}][type]" 
                                               value="{{ old('type', optional($translation)->type) }}">
                                    </div>
                                @endforeach
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                <a href="{{ route('admin.roulette-actions.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('type');
    const percentField = document.getElementById('percentField');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');

    function togglePercentField() {
        if (typeSelect.value === 'percent') {
            percentField.classList.remove('d-none');
        } else {
            percentField.classList.add('d-none');
        }
    }

    typeSelect.addEventListener('change', togglePercentField);
    togglePercentField();

    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });
});
</script>
@endpush
