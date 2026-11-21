@php $cls = 'w-full px-4 py-2.5 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-secondary/50'; @endphp
<form method="POST" action="{{ $address ? route('addresses.update', $address) : route('addresses.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-xl bg-surface">
    @csrf
    @if($address) @method('PUT') @endif
    <input class="{{ $cls }}" name="label" required placeholder="Label (Home, Studio...)" value="{{ $address->label ?? '' }}">
    <select class="{{ $cls }}" name="icon">
        @foreach(['home' => 'Home', 'apartment' => 'Apartment / Office', 'storefront' => 'Shop', 'favorite' => 'Family'] as $ic => $lbl)
            <option value="{{ $ic }}" @selected(($address->icon ?? 'home') === $ic)>{{ $lbl }}</option>
        @endforeach
    </select>
    <input class="{{ $cls }} sm:col-span-2" name="line1" required placeholder="Flat / House, Building, Street" value="{{ $address->line1 ?? '' }}">
    <input class="{{ $cls }} sm:col-span-2" name="line2" placeholder="Area, City - Pincode" value="{{ $address->line2 ?? '' }}">
    <input class="{{ $cls }}" name="recipient" placeholder="Recipient name" value="{{ $address->recipient ?? '' }}">
    <input class="{{ $cls }}" name="phone" placeholder="Recipient phone" value="{{ $address->phone ?? '' }}">
    <input class="{{ $cls }} sm:col-span-2" name="note" placeholder="Delivery note (gate, security...)" value="{{ $address->note ?? '' }}">
    <label class="flex items-center gap-2 font-body-sm text-body-sm"><input type="checkbox" name="is_default" value="1" class="accent-secondary" @checked($address->is_default ?? false)> Set as default</label>
    <div class="flex justify-end"><button class="px-5 py-2 rounded-full bg-primary text-on-primary font-label-md text-label-md hover:bg-secondary">{{ $address ? 'Update Address' : 'Save Address' }}</button></div>
</form>
