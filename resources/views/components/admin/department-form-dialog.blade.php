@props(['employees'])

@php
    $submittedMode = old('_form');
    $editingId = $submittedMode === 'edit' ? old('_department_id') : null;
    $isEditSubmission = filled($editingId);
    $formAction = $isEditSubmission
        ? route('admin.departments.update', $editingId)
        : route('admin.departments.store');

    $fieldClasses = 'mt-1 h-10 w-full rounded-lg border bg-surface-container-low px-3 font-body-sm text-body-sm text-on-surface transition-colors focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
    $labelClasses = 'font-label-sm text-label-sm font-semibold text-on-surface';
    $errorClasses = 'mt-1 font-label-sm text-label-sm text-error';
@endphp

<x-ui.modal
    id="department-form-dialog"
    :title="$isEditSubmission ? 'Modifier le département' : 'Nouveau département'"
    description="Les informations structurelles et budgétaires du département."
>
    <form class="space-y-5" id="department-form" method="POST" action="{{ $formAction }}" novalidate>
        @csrf
        <input type="hidden" name="_form" id="department-form-mode" value="{{ old('_form', 'create') }}"/>
        <input type="hidden" name="_department_id" id="department-dialog-id" value="{{ old('_department_id') }}"/>
        <input type="hidden" name="_method" id="department-dialog-method" value="{{ $isEditSubmission ? 'PUT' : 'POST' }}"/>

        <p class="flex items-start gap-2 rounded-lg border border-outline-variant/60 bg-surface-container-low p-3 font-body-sm text-body-sm text-on-surface-variant" id="department-create-notice" @class(['hidden' => $isEditSubmission])>
            <span class="material-symbols-outlined shrink-0 text-body-md" aria-hidden="true">info</span>
            <span>Les collaborateurs restent rattachés à cinq structures de référence gérées par le module Collaborateurs. Une nouvelle structure est disponible pour le suivi budgétaire, sans affectation automatique.</span>
        </p>

        <div>
            <label class="{{ $labelClasses }}" for="department-name">Nom du département <span class="text-error" aria-hidden="true">*</span></label>
            <input
                @class([$fieldClasses, 'border-error' => $errors->has('name'), 'border-outline-variant' => ! $errors->has('name')])
                id="department-name"
                name="name"
                type="text"
                maxlength="120"
                required
                value="{{ old('name') }}"
                placeholder="Ex. Data &amp; Analytics"
                @error('name') aria-invalid="true" aria-describedby="department-name-error" @enderror
            />
            @error('name')
                <p class="{{ $errorClasses }}" id="department-name-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="{{ $labelClasses }}" for="department-manager">Responsable</label>
            <select
                @class([$fieldClasses, 'border-error' => $errors->has('manager_id'), 'border-outline-variant' => ! $errors->has('manager_id')])
                id="department-manager"
                name="manager_id"
                @error('manager_id') aria-invalid="true" aria-describedby="department-manager-error" @enderror
            >
                <option value="">Aucun responsable assigné</option>
                @foreach ($employees as $employee)
                    <option
                        value="{{ $employee->id }}"
                        @selected((string) old('manager_id') === (string) $employee->id)
                        @disabled($employee->status === 'inactif')
                    >
                        {{ $employee->name }} — {{ $employee->position }}@if ($employee->status === 'inactif') (inactif)@endif
                    </option>
                @endforeach
            </select>
            @error('manager_id')
                <p class="{{ $errorClasses }}" id="department-manager-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $labelClasses }}" for="department-code">Code analytique</label>
                <input
                    @class([$fieldClasses, 'border-error' => $errors->has('code'), 'border-outline-variant' => ! $errors->has('code')])
                    id="department-code"
                    name="code"
                    type="text"
                    maxlength="50"
                    value="{{ old('code') }}"
                    placeholder="Ex. CC-TECH"
                    @error('code') aria-invalid="true" aria-describedby="department-code-error" @enderror
                />
                @error('code')
                    <p class="{{ $errorClasses }}" id="department-code-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="{{ $labelClasses }}" for="department-budget">Budget mensuel (TND)</label>
                <input
                    @class([$fieldClasses, 'border-error' => $errors->has('monthly_budget'), 'border-outline-variant' => ! $errors->has('monthly_budget')])
                    id="department-budget"
                    name="monthly_budget"
                    type="number"
                    inputmode="decimal"
                    min="0"
                    max="999999999.999"
                    step="0.001"
                    value="{{ old('monthly_budget') }}"
                    placeholder="Ex. 25000"
                    aria-describedby="department-budget-help @error('monthly_budget') department-budget-error @enderror"
                />
                <p class="mt-1 font-label-sm text-label-sm text-on-surface-variant" id="department-budget-help">Montant brut mensuel, trois décimales maximum.</p>
                @error('monthly_budget')
                    <p class="{{ $errorClasses }}" id="department-budget-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="{{ $labelClasses }}" for="department-description">Description</label>
            <textarea
                @class([$fieldClasses, 'h-auto py-2', 'border-error' => $errors->has('description'), 'border-outline-variant' => ! $errors->has('description')])
                id="department-description"
                name="description"
                rows="3"
                maxlength="1000"
                placeholder="Périmètre, missions ou objectifs du département."
                @error('description') aria-invalid="true" aria-describedby="department-description-error" @enderror
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="{{ $errorClasses }}" id="department-description-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-surface-container pt-5 sm:flex-row sm:justify-end">
            <button
                class="h-10 rounded-lg bg-surface-container-low px-4 font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary"
                type="button"
                onclick="document.getElementById('department-form-dialog').close()"
            >
                Annuler
            </button>
            <button
                class="h-10 rounded-lg bg-primary-container px-4 font-label-md text-label-md text-on-primary transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                type="submit"
            >
                <span id="department-submit-label">{{ $isEditSubmission ? 'Enregistrer les modifications' : 'Créer le département' }}</span>
            </button>
        </div>
    </form>
</x-ui.modal>
