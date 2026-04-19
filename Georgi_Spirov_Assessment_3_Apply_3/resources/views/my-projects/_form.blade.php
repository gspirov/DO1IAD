@csrf

<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text"
           name="title"
           required
           x-model="title"
           @input="clearBackendError('title')"
           :class="{ 'is-invalid': hasBackendError('title') || (shouldShow('title') && (!hasFilledAttribute('title') || !hasTitleValidLength())) }"
           class="form-control"
    >

    @error('title')
        <div x-show="hasBackendError('title')" class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div x-show="!hasBackendError('title') && submitted && !hasFilledAttribute('title')" class="invalid-feedback">
        {{ trans('validation.required', ['attribute' => 'title']) }}
    </div>

    <div x-show="!hasBackendError('start_date') && !hasTitleValidLength()" class="invalid-feedback">
        {{ trans('validation.max.string', ['attribute' => 'title', 'max' => 255]) }}
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Short Description</label>
    <textarea name="short_description"
              required
              x-model="short_description"
              @input="clearBackendError('short_description')"
              :class="{ 'is-invalid': hasBackendError('short_description') || (shouldShow('short_description') && !hasFilledAttribute('short_description')) }"
              class="form-control"
    ></textarea>

    @error('short_description')
        <div x-show="hasBackendError('short_description')" class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div x-show="!hasBackendError('short_description') && submitted && !hasFilledAttribute('short_description')" class="invalid-feedback">
        {{ trans('validation.required', ['attribute' => 'short description']) }}
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Start Date</label>
    <input type="date"
           name="start_date"
           required
           x-model="start_date"
           @input="clearBackendError('start_date')"
           :class="{ 'is-invalid': hasBackendError('start_date') || (shouldShow('start_date') && !hasFilledAttribute('start_date')) }"
           class="form-control"
    >

    @error('start_date')
        <div x-show="hasBackendError('start_date')" class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div x-show="!hasBackendError('start_date') && submitted && !hasFilledAttribute('start_date')" class="invalid-feedback">
        {{ trans('validation.required', ['attribute' => 'start date']) }}
    </div>
</div>

<div class="mb-3">
    <label class="form-label">End Date</label>
    <input type="date"
           name="end_date"
           required
           x-model="end_date"
           @input="clearBackendError('end_date')"
           :class="{ 'is-invalid': hasBackendError('start_date') || (shouldShow('end_date') && (!hasFilledAttribute('end_date') || areDatesInvalid())) }"
           class="form-control"
    >

    @error('start_date')
        <div x-show="hasBackendError('end_date')" class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div x-show="!hasBackendError('end_date') && submitted && !hasFilledAttribute('end_date')" class="invalid-feedback">
        {{ trans('validation.required', ['attribute' => 'end date']) }}
    </div>

    <div x-show="!hasBackendError('end_date') && areDatesInvalid()" class="invalid-feedback">
        {{ trans('validation.after', ['attribute' => 'end date', 'date' => 'start date']) }}
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Phase</label>
    <select name="phase"
            required
            x-model="phase"
            @input="clearBackendError('phase')"
            :class="{ 'is-invalid': hasBackendError('phase') || (shouldShow('phase') && !hasFilledAttribute('phase')) }"
            class="form-control"
    >
        <option value="">Select phase</option>

        @foreach($phases as $phase)
            <option value="{{ $phase->value }}">
                {{ $phase->value }}
            </option>
        @endforeach
    </select>

    @error('phase')
        <div x-show="hasBackendError('phase')" class="invalid-feedback">{{ $message }}</div>
    @enderror

    <div x-show="!hasBackendError('phase') && submitted && !hasFilledAttribute('phase')" class="invalid-feedback">
        {{ trans('validation.required', ['attribute' => 'phase']) }}
    </div>
</div>

<div class="d-flex">
    <button type="submit" class="btn btn-primary px-4" :disabled="invalid()">
        Save
    </button>
</div>
