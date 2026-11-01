@if ($errors->any())
<div class="mb-space-md p-space-md rounded-xl bg-error-container text-on-error-container flex items-start gap-space-sm shadow-sm" role="alert">
    <span class="material-symbols-outlined text-body-lg shrink-0 mt-0.5">error</span>
    <div class="flex flex-col gap-1">
        <span class="font-label-md text-label-md font-semibold">Veuillez corriger les informations suivantes</span>
        <ul class="list-disc list-inside font-body-sm text-body-sm space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif
